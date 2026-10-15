<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Authorization\RoleManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class RoleController extends Controller
{
    /** @var array<string, string> */
    private const APPLICATION_ROLES = [
        'admin' => 'Administrator role for the administration panel',
        'customer' => 'Customer role for customer accounts',
    ];

    public function index(Request $request, RoleManagementService $roleManagementService): View
    {
        try {
            $roles = $roleManagementService->listRoles($this->accessToken($request))['roles'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The authorization service is temporarily unavailable.');
        }

        return view('admin.roles.index', ['roles' => $roles]);
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request, RoleManagementService $roleManagementService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $response = $roleManagementService->createRole($this->accessToken($request), $validated['name'], $validated['description'] ?? '');
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'The role could not be created. Please try again.'])->withInput();
        }

        $roleId = data_get($response, 'role.id');

        return is_string($roleId)
            ? redirect()->route('admin.roles.show', $roleId)->with('status', 'Role created.')
            : redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function sync(Request $request, RoleManagementService $roleManagementService): RedirectResponse
    {
        try {
            $token = $this->accessToken($request);
            $existingRoles = $roleManagementService->listRoles($token)['roles'] ?? [];
            $existingByName = collect($existingRoles)->keyBy('name');
            $created = 0;
            $updated = 0;

            foreach (self::APPLICATION_ROLES as $name => $description) {
                $role = $existingByName->get($name);

                if (! is_array($role)) {
                    $roleManagementService->createRole($token, $name, $description);
                    $created++;

                    continue;
                }

                if (($role['description'] ?? '') !== $description && is_string($role['id'] ?? null)) {
                    $roleManagementService->updateRole(
                        $token,
                        $role['id'],
                        $name,
                        $description,
                        (bool) ($role['is_active'] ?? false),
                    );
                    $updated++;
                }
            }
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'Application roles could not be synchronized. Please try again.']);
        }

        $unchanged = count(self::APPLICATION_ROLES) - $created - $updated;

        return redirect()->route('admin.roles.index')->with(
            'status',
            "Application roles synchronized. Created: {$created}; updated: {$updated}; unchanged: {$unchanged}.",
        );
    }

    public function show(Request $request, string $roleId, RoleManagementService $roleManagementService): View
    {
        try {
            $token = $this->accessToken($request);
            $roles = $roleManagementService->listRoles($token)['roles'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The authorization service is temporarily unavailable.');
        }

        $role = collect($roles)->first(fn (array $candidate): bool => ($candidate['id'] ?? null) === $roleId);
        abort_unless(is_array($role), 404);

        try {
            $allPermissions = $roleManagementService->listPermissions($token)['permissions'] ?? [];
            $rolePermissions = $roleManagementService->listRolePermissions($token, $roleId)['permissions'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The authorization service is temporarily unavailable.');
        }

        $assignedIds = collect($rolePermissions)->pluck('id')->all();

        return view('admin.roles.show', [
            'role' => $role,
            'permissions' => $allPermissions,
            'assignedPermissionIds' => $assignedIds,
        ]);
    }

    public function update(Request $request, string $roleId, RoleManagementService $roleManagementService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            $token = $this->accessToken($request);
            $roles = $roleManagementService->listRoles($token)['roles'] ?? [];
            $currentRole = collect($roles)->first(fn (array $role): bool => ($role['id'] ?? null) === $roleId);

            if (($currentRole['name'] ?? null) === 'superadmin'
                && ($validated['name'] !== 'superadmin' || ! (bool) $validated['is_active'])) {
                return back()->withErrors(['role' => 'The superadmin role cannot be renamed or deactivated.']);
            }

            $roleManagementService->updateRole(
                $token,
                $roleId,
                $validated['name'],
                $validated['description'] ?? '',
                (bool) $validated['is_active'],
            );
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'The role could not be updated. Please try again.']);
        }

        return back()->with('status', 'Role updated.');
    }

    public function destroy(Request $request, string $roleId, RoleManagementService $roleManagementService): RedirectResponse
    {
        try {
            $token = $this->accessToken($request);
            $roles = $roleManagementService->listRoles($token)['roles'] ?? [];
            $role = collect($roles)->first(fn (array $candidate): bool => ($candidate['id'] ?? null) === $roleId);

            if (($role['name'] ?? null) === 'superadmin') {
                return back()->withErrors(['role' => 'The superadmin role is protected from deletion.']);
            }

            $roleManagementService->deleteRole($token, $roleId);
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'The role could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }

    public function grantPermission(Request $request, string $roleId, RoleManagementService $roleManagementService): RedirectResponse
    {
        $validated = $request->validate(['permission_id' => ['required', 'uuid']]);

        try {
            $token = $this->accessToken($request);
            $permissions = $roleManagementService->listPermissions($token)['permissions'] ?? [];
        } catch (RuntimeException) {
            return back()->withErrors(['permission_id' => 'The permission could not be granted. Please try again.']);
        }

        abort_unless(collect($permissions)->contains(fn (array $permission): bool => ($permission['id'] ?? null) === $validated['permission_id']), 404);

        try {
            $roleManagementService->grantPermission($token, $roleId, $validated['permission_id']);
        } catch (RuntimeException) {
            return back()->withErrors(['permission_id' => 'The permission could not be granted. Please try again.']);
        }

        return back()->with('status', 'Permission granted.');
    }

    public function revokePermission(Request $request, string $roleId, RoleManagementService $roleManagementService): RedirectResponse
    {
        $validated = $request->validate(['permission_id' => ['required', 'uuid']]);

        try {
            $token = $this->accessToken($request);
            $permissions = $roleManagementService->listPermissions($token)['permissions'] ?? [];
        } catch (RuntimeException) {
            return back()->withErrors(['permission_id' => 'The permission could not be revoked. Please try again.']);
        }

        abort_unless(collect($permissions)->contains(fn (array $permission): bool => ($permission['id'] ?? null) === $validated['permission_id']), 404);

        try {
            $roleManagementService->revokePermission($token, $roleId, $validated['permission_id']);
        } catch (RuntimeException) {
            return back()->withErrors(['permission_id' => 'The permission could not be revoked. Please try again.']);
        }

        return back()->with('status', 'Permission revoked.');
    }

    private function accessToken(Request $request): string
    {
        $token = $request->session()->get('admin.access_token');
        abort_unless(is_string($token) && $token !== '', 401);

        return $token;
    }
}
