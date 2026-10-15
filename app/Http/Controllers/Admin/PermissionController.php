<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Authorization\PermissionCatalog;
use App\Services\Authorization\PermissionManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PermissionController extends Controller
{
    public function index(Request $request, PermissionManagementService $permissionManagementService): View
    {
        try {
            $permissions = $permissionManagementService->listPermissions($this->accessToken($request))['permissions'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The authorization service is temporarily unavailable.');
        }

        return view('admin.permissions.index', ['permissions' => $permissions]);
    }

    public function create(): View
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request, PermissionManagementService $permissionManagementService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'regex:/^[a-z][a-z0-9_.-]*$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $permissionManagementService->createPermission($this->accessToken($request), $validated['name'], $validated['description'] ?? '');
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'The permission could not be created. Please try again.'])->withInput();
        }

        return redirect()->route('admin.permissions.index')->with('status', 'Permission created.');
    }

    public function seed(Request $request, PermissionCatalog $permissionCatalog, PermissionManagementService $permissionManagementService): RedirectResponse
    {
        try {
            $token = $this->accessToken($request);
            $existingPermissions = $permissionManagementService->listPermissions($token)['permissions'] ?? [];
            $existingByName = collect($existingPermissions)->keyBy('name');
            $permissions = $permissionCatalog->checked();
            $created = 0;
            $updated = 0;

            foreach ($permissions as $name => $description) {
                $existing = $existingByName->get($name);

                if (! is_array($existing)) {
                    $permissionManagementService->createPermission($token, $name, $description);
                    $created++;

                    continue;
                }

                if (($existing['description'] ?? '') !== $description && is_string($existing['id'] ?? null)) {
                    $permissionManagementService->updatePermission($token, $existing['id'], $name, $description);
                    $updated++;
                }
            }
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'Application permissions could not be synchronized. Please try again.']);
        }

        $unchanged = count($permissions) - $created - $updated;

        return redirect()->route('admin.permissions.index')->with(
            'status',
            "Application permissions synchronized. Created: {$created}; updated: {$updated}; unchanged: {$unchanged}.",
        );
    }

    public function update(Request $request, string $permissionId, PermissionManagementService $permissionManagementService, PermissionCatalog $permissionCatalog): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'regex:/^[a-z][a-z0-9_.-]*$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $token = $this->accessToken($request);
            $permissions = $permissionManagementService->listPermissions($token)['permissions'] ?? [];
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'The permission could not be updated. Please try again.']);
        }

        $currentPermission = collect($permissions)->first(fn (array $permission): bool => ($permission['id'] ?? null) === $permissionId);
        abort_unless(is_array($currentPermission), 404);

        if (array_key_exists($currentPermission['name'], $permissionCatalog->all()) && $validated['name'] !== $currentPermission['name']) {
            return back()->withErrors(['permission' => 'Application catalog permissions cannot be renamed.']);
        }

        try {
            $permissionManagementService->updatePermission($token, $permissionId, $validated['name'], $validated['description'] ?? '');
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'The permission could not be updated. Please try again.']);
        }

        return back()->with('status', 'Permission updated.');
    }

    public function destroy(Request $request, string $permissionId, PermissionManagementService $permissionManagementService, PermissionCatalog $permissionCatalog): RedirectResponse
    {
        try {
            $token = $this->accessToken($request);
            $permissions = $permissionManagementService->listPermissions($token)['permissions'] ?? [];
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'The permission could not be deleted. Please try again.']);
        }

        $permission = collect($permissions)->first(fn (array $candidate): bool => ($candidate['id'] ?? null) === $permissionId);
        abort_unless(is_array($permission), 404);

        if (array_key_exists($permission['name'], $permissionCatalog->all())) {
            return back()->withErrors(['permission' => 'Application catalog permissions cannot be deleted.']);
        }

        try {
            $permissionManagementService->deletePermission($token, $permissionId);
        } catch (RuntimeException) {
            return back()->withErrors(['permission' => 'The permission could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.permissions.index')->with('status', 'Permission deleted.');
    }

    private function accessToken(Request $request): string
    {
        $token = $request->session()->get('admin.access_token');
        abort_unless(is_string($token) && $token !== '', 401);

        return $token;
    }
}
