<?php

namespace App\Services\Authorization;

use RuntimeException;

class CustomerRoleSeeder
{
    private const ROLE_NAME = 'customer';

    private const ROLE_DESCRIPTION = 'Storefront customer with access to browse products and manage own orders';

    public function __construct(
        private PermissionCatalog $permissionCatalog,
        private PermissionManagementService $permissionManagementService,
        private RoleManagementService $roleManagementService,
    ) {}

    /** @return array{role_created: bool, role_updated: bool, grants_added: int, grants_removed: int} */
    public function synchronize(string $token): array
    {
        $role = $this->synchronizeRole($token);
        $roleId = $role['id'] ?? null;

        if (! is_string($roleId) || $roleId === '') {
            throw new RuntimeException('The authorization service returned an invalid customer role.');
        }

        $permissions = $this->permissionManagementService->listPermissions($token)['permissions'] ?? [];
        $permissionsByName = collect($permissions)->keyBy('name');
        $desiredPermissionIds = collect($this->permissionCatalog->customerPermissions())
            ->keys()
            ->map(fn (string $name): mixed => $permissionsByName->get($name)['id'] ?? null)
            ->filter(fn (mixed $id): bool => is_string($id) && $id !== '')
            ->values();

        if ($desiredPermissionIds->count() !== count($this->permissionCatalog->customerPermissions())) {
            throw new RuntimeException('Customer permissions are missing. Synchronize application permissions before application roles.');
        }

        $currentPermissionIds = collect(
            $this->roleManagementService->listRolePermissions($token, $roleId)['permissions'] ?? [],
        )->pluck('id');
        $grantsAdded = 0;
        $grantsRemoved = 0;

        foreach ($desiredPermissionIds->diff($currentPermissionIds) as $permissionId) {
            $this->roleManagementService->grantPermission($token, $roleId, $permissionId);
            $grantsAdded++;
        }

        foreach ($currentPermissionIds->diff($desiredPermissionIds) as $permissionId) {
            $this->roleManagementService->revokePermission($token, $roleId, $permissionId);
            $grantsRemoved++;
        }

        return [
            'role_created' => $role['created'],
            'role_updated' => $role['updated'],
            'grants_added' => $grantsAdded,
            'grants_removed' => $grantsRemoved,
        ];
    }

    /** @return array{id: string, created: bool, updated: bool} */
    private function synchronizeRole(string $token): array
    {
        $roles = $this->roleManagementService->listRoles($token)['roles'] ?? [];
        $role = collect($roles)->first(fn (array $candidate): bool => ($candidate['name'] ?? null) === self::ROLE_NAME);

        if (! is_array($role)) {
            $createdRole = $this->roleManagementService->createRole($token, self::ROLE_NAME, self::ROLE_DESCRIPTION)['role'] ?? null;
            $roleId = is_array($createdRole) ? ($createdRole['id'] ?? null) : null;

            if (! is_string($roleId) || $roleId === '') {
                throw new RuntimeException('The authorization service did not return the created customer role.');
            }

            return ['id' => $roleId, 'created' => true, 'updated' => false];
        }

        $roleId = $role['id'] ?? null;

        if (! is_string($roleId) || $roleId === '') {
            throw new RuntimeException('The authorization service returned an invalid customer role.');
        }

        $isActive = (bool) ($role['is_active'] ?? false);
        $description = (string) ($role['description'] ?? '');
        $updated = ! $isActive || $description !== self::ROLE_DESCRIPTION;

        if ($updated) {
            $this->roleManagementService->updateRole(
                $token,
                $roleId,
                self::ROLE_NAME,
                self::ROLE_DESCRIPTION,
                true,
            );
        }

        return ['id' => $roleId, 'created' => false, 'updated' => $updated];
    }
}
