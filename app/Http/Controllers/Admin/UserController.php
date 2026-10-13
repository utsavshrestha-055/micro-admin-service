<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Users\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class UserController extends Controller
{
    public function index(Request $request, UserManagementService $userManagementService): View
    {
        $page = min(max(1, $request->integer('page', 1)), 100000);
        $query = Str::lower(trim(Str::limit($request->string('q')->toString(), 100, '')));
        $status = $request->string('status')->toString();

        try {
            $response = $userManagementService->listUsers($this->accessToken($request), 26, ($page - 1) * 25);
        } catch (RuntimeException) {
            abort(503, 'The user service is temporarily unavailable.');
        }

        $pageUsers = array_slice($response['users'] ?? [], 0, 25);
        $users = collect($pageUsers);

        if ($query !== '') {
            $users = $users->filter(fn (array $user): bool => str_contains(Str::lower((string) ($user['email'] ?? '')), $query)
                || str_contains(Str::lower(trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? ''))), $query));
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $isActive = $status === 'active';
            $users = $users->filter(fn (array $user): bool => (bool) ($user['is_active'] ?? false) === $isActive);
        }

        return view('admin.users.index', [
            'users' => $users->values(),
            'page' => $page,
            'query' => $query,
            'status' => $status,
            'hasNextPage' => count($response['users'] ?? []) > 25,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request, UserManagementService $userManagementService): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:12', 'max:1024', 'confirmed'],
        ]);

        try {
            $userManagementService->createUser(
                $validated['email'],
                $validated['password'],
                $validated['first_name'],
                $validated['last_name'] ?? '',
            );
        } catch (RuntimeException) {
            return back()
                ->withErrors(['user' => 'The user could not be created. Check whether the email is already registered.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function show(Request $request, string $userId, UserManagementService $userManagementService): View
    {
        try {
            $token = $this->accessToken($request);
            $response = $userManagementService->getUser($token, $userId);
            $roles = $userManagementService->listUserRoles($token, $userId);
            $availableRoles = $userManagementService->listRoles($token);
            $effectivePermissions = collect($availableRoles['roles'] ?? [])
                ->filter(fn (array $role): bool => in_array($role['name'] ?? null, $roles['values'] ?? [], true))
                ->flatMap(fn (array $role): array => $userManagementService->listRolePermissions($token, (string) $role['id'])['permissions'] ?? [])
                ->unique('id')
                ->values();
        } catch (RuntimeException) {
            abort(503, 'The user service is temporarily unavailable.');
        }

        abort_unless(is_array($response['user'] ?? null), 404);

        return view('admin.users.show', [
            'user' => $response['user'],
            'roles' => $roles['values'] ?? [],
            'availableRoles' => $availableRoles['roles'] ?? [],
            'effectivePermissions' => $effectivePermissions,
        ]);
    }

    public function update(Request $request, string $userId, UserManagementService $userManagementService): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:12', 'max:1024'],
        ]);

        if ($userId === (string) data_get($request->session()->get('admin.user'), 'id') && ! (bool) $validated['is_active']) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        try {
            $userManagementService->updateUser($this->accessToken($request), $userId, [
                ...$validated,
                'first_name' => $validated['first_name'] ?? '',
                'last_name' => $validated['last_name'] ?? '',
                'is_active' => (bool) $validated['is_active'],
                'password' => $validated['password'] ?? '',
            ]);
        } catch (RuntimeException) {
            return back()->withErrors(['user' => 'The user could not be updated. Please try again.']);
        }

        return back()->with('status', 'User updated.');
    }

    public function destroy(Request $request, string $userId, UserManagementService $userManagementService): RedirectResponse
    {
        if ($userId === (string) data_get($request->session()->get('admin.user'), 'id')) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        try {
            $userManagementService->deleteUser($this->accessToken($request), $userId);
        } catch (RuntimeException) {
            return back()->withErrors(['user' => 'The user could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }

    public function assignRole(Request $request, string $userId, UserManagementService $userManagementService): RedirectResponse
    {
        $validated = $request->validate(['role' => ['required', 'string', 'max:100']]);
        $token = $this->accessToken($request);

        try {
            $roles = $userManagementService->listRoles($token)['roles'] ?? [];
            $roleExists = collect($roles)->contains(fn (array $role): bool => ($role['name'] ?? null) === $validated['role']);

            if (! $roleExists) {
                return back()->withErrors(['role' => 'Select a role returned by the authorization service.']);
            }

            $userManagementService->assignRole($token, $userId, $validated['role']);
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'The role could not be assigned. Please try again.']);
        }

        return back()->with('status', 'Role assigned.');
    }

    public function unassignRole(Request $request, string $userId, UserManagementService $userManagementService): RedirectResponse
    {
        $validated = $request->validate(['role' => ['required', 'string', 'max:100']]);

        try {
            $token = $this->accessToken($request);
            $roles = $userManagementService->listRoles($token)['roles'] ?? [];
            $roleExists = collect($roles)->contains(fn (array $role): bool => ($role['name'] ?? null) === $validated['role']);

            if (! $roleExists) {
                return back()->withErrors(['role' => 'The requested role is not available.']);
            }

            if ($userId === (string) data_get($request->session()->get('admin.user'), 'id') && $validated['role'] === 'superadmin') {
                return back()->withErrors(['role' => 'You cannot remove your own superadmin role.']);
            }

            $userManagementService->unassignRole($token, $userId, $validated['role']);
        } catch (RuntimeException) {
            return back()->withErrors(['role' => 'The role could not be removed. Please try again.']);
        }

        return back()->with('status', 'Role removed.');
    }

    private function accessToken(Request $request): string
    {
        $token = $request->session()->get('admin.access_token');

        abort_unless(is_string($token) && $token !== '', 401);

        return $token;
    }
}
