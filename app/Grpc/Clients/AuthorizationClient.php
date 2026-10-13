<?php

namespace App\Grpc\Clients;

use App\Grpc\GrpcTransport;

class AuthorizationClient
{
    public function __construct(private GrpcTransport $transport) {}

    /** @return array<string, mixed> */
    public function checkPermission(string $accessToken, string $permission): array
    {
        return $this->transport->call('AuthorizationService', 'CheckPermission', [
            'accessToken' => $accessToken,
            'permission' => $permission,
        ]);
    }

    /** @return array<string, mixed> */
    public function listRoles(string $accessToken): array
    {
        return $this->transport->call('AuthorizationService', 'ListRoles', ['accessToken' => $accessToken]);
    }

    /** @return array<string, mixed> */
    public function createRole(string $accessToken, string $name, string $description): array
    {
        return $this->transport->call('AuthorizationService', 'CreateRole', [
            'accessToken' => $accessToken,
            'name' => $name,
            'description' => $description,
        ]);
    }

    /** @return array<string, mixed> */
    public function updateRole(string $accessToken, string $roleId, string $name, string $description, bool $isActive): array
    {
        return $this->transport->call('AuthorizationService', 'UpdateRole', [
            'accessToken' => $accessToken,
            'roleId' => $roleId,
            'name' => $name,
            'description' => $description,
            'isActive' => $isActive,
        ]);
    }

    /** @return array<string, mixed> */
    public function deleteRole(string $accessToken, string $roleId): array
    {
        return $this->transport->call('AuthorizationService', 'DeleteRole', [
            'accessToken' => $accessToken,
            'roleId' => $roleId,
        ]);
    }

    /** @return array<string, mixed> */
    public function listPermissions(string $accessToken): array
    {
        return $this->transport->call('AuthorizationService', 'ListPermissions', ['accessToken' => $accessToken]);
    }

    /** @return array<string, mixed> */
    public function createPermission(string $accessToken, string $name, string $description): array
    {
        return $this->transport->call('AuthorizationService', 'CreatePermission', [
            'accessToken' => $accessToken,
            'name' => $name,
            'description' => $description,
        ]);
    }

    /** @return array<string, mixed> */
    public function updatePermission(string $accessToken, string $permissionId, string $name, string $description): array
    {
        return $this->transport->call('AuthorizationService', 'UpdatePermission', [
            'accessToken' => $accessToken,
            'permissionId' => $permissionId,
            'name' => $name,
            'description' => $description,
        ]);
    }

    /** @return array<string, mixed> */
    public function deletePermission(string $accessToken, string $permissionId): array
    {
        return $this->transport->call('AuthorizationService', 'DeletePermission', [
            'accessToken' => $accessToken,
            'permissionId' => $permissionId,
        ]);
    }

    /** @return array<string, mixed> */
    public function assignRole(string $accessToken, string $userId, string $role): array
    {
        return $this->transport->call('AuthorizationService', 'AssignRole', [
            'accessToken' => $accessToken,
            'userId' => $userId,
            'role' => $role,
        ]);
    }

    /** @return array<string, mixed> */
    public function unassignRole(string $accessToken, string $userId, string $role): array
    {
        return $this->transport->call('AuthorizationService', 'UnassignRole', [
            'accessToken' => $accessToken,
            'userId' => $userId,
            'role' => $role,
        ]);
    }

    /** @return array<string, mixed> */
    public function listUserRoles(string $accessToken, string $userId): array
    {
        return $this->transport->call('AuthorizationService', 'ListUserRoles', [
            'accessToken' => $accessToken,
            'userId' => $userId,
        ]);
    }

    /** @return array<string, mixed> */
    public function listRolePermissions(string $accessToken, string $roleId): array
    {
        return $this->transport->call('AuthorizationService', 'ListRolePermissions', [
            'accessToken' => $accessToken,
            'roleId' => $roleId,
        ]);
    }

    /** @return array<string, mixed> */
    public function grantPermissionToRole(string $accessToken, string $roleId, string $permissionId): array
    {
        return $this->transport->call('AuthorizationService', 'GrantPermissionToRole', [
            'accessToken' => $accessToken,
            'roleId' => $roleId,
            'permissionId' => $permissionId,
        ]);
    }

    /** @return array<string, mixed> */
    public function revokePermissionFromRole(string $accessToken, string $roleId, string $permissionId): array
    {
        return $this->transport->call('AuthorizationService', 'RevokePermissionFromRole', [
            'accessToken' => $accessToken,
            'roleId' => $roleId,
            'permissionId' => $permissionId,
        ]);
    }
}
