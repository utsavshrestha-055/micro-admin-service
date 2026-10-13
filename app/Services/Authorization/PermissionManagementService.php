<?php

namespace App\Services\Authorization;

use App\Grpc\Clients\AuthorizationClient;

class PermissionManagementService
{
    public function __construct(private AuthorizationClient $authorizationClient) {}

    /** @return array<string, mixed> */
    public function listPermissions(string $token): array
    {
        return $this->authorizationClient->listPermissions($token);
    }

    /** @return array<string, mixed> */
    public function createPermission(string $token, string $name, string $description): array
    {
        return $this->authorizationClient->createPermission($token, $name, $description);
    }

    /** @return array<string, mixed> */
    public function updatePermission(string $token, string $permissionId, string $name, string $description): array
    {
        return $this->authorizationClient->updatePermission($token, $permissionId, $name, $description);
    }

    /** @return array<string, mixed> */
    public function deletePermission(string $token, string $permissionId): array
    {
        return $this->authorizationClient->deletePermission($token, $permissionId);
    }
}
