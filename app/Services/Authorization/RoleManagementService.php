<?php

namespace App\Services\Authorization;

use App\Grpc\Clients\AuthorizationClient;

class RoleManagementService
{
    public function __construct(private AuthorizationClient $authorizationClient) {}

    /** @return array<string, mixed> */
    public function listRoles(string $token): array
    {
        return $this->authorizationClient->listRoles($token);
    }

    /** @return array<string, mixed> */
    public function createRole(string $token, string $name, string $description): array
    {
        return $this->authorizationClient->createRole($token, $name, $description);
    }

    /** @return array<string, mixed> */
    public function updateRole(string $token, string $roleId, string $name, string $description, bool $active): array
    {
        return $this->authorizationClient->updateRole($token, $roleId, $name, $description, $active);
    }

    /** @return array<string, mixed> */
    public function deleteRole(string $token, string $roleId): array
    {
        return $this->authorizationClient->deleteRole($token, $roleId);
    }

    /** @return array<string, mixed> */
    public function listPermissions(string $token): array
    {
        return $this->authorizationClient->listPermissions($token);
    }

    /** @return array<string, mixed> */
    public function listRolePermissions(string $token, string $roleId): array
    {
        return $this->authorizationClient->listRolePermissions($token, $roleId);
    }

    /** @return array<string, mixed> */
    public function grantPermission(string $token, string $roleId, string $permissionId): array
    {
        return $this->authorizationClient->grantPermissionToRole($token, $roleId, $permissionId);
    }

    /** @return array<string, mixed> */
    public function revokePermission(string $token, string $roleId, string $permissionId): array
    {
        return $this->authorizationClient->revokePermissionFromRole($token, $roleId, $permissionId);
    }
}
