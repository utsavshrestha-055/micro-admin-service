<?php

namespace App\Services\Users;

use App\Grpc\Clients\AuthenticationClient;
use App\Grpc\Clients\AuthorizationClient;

class UserManagementService
{
    public function __construct(
        private AuthenticationClient $authenticationClient,
        private AuthorizationClient $authorizationClient,
    ) {}

    /** @return array<string, mixed> */
    public function listUsers(string $token, int $limit, int $offset): array
    {
        return $this->authenticationClient->listUsers($token, $limit, $offset);
    }

    /** @return array<string, mixed> */
    public function getUser(string $token, string $userId): array
    {
        return $this->authenticationClient->getUser($token, $userId);
    }

    /** @return array<string, mixed> */
    public function listUserRoles(string $token, string $userId): array
    {
        return $this->authorizationClient->listUserRoles($token, $userId);
    }

    /** @return array<string, mixed> */
    public function listRoles(string $token): array
    {
        return $this->authorizationClient->listRoles($token);
    }

    /** @return array<string, mixed> */
    public function listRolePermissions(string $token, string $roleId): array
    {
        return $this->authorizationClient->listRolePermissions($token, $roleId);
    }

    /** @param array<string, mixed> $user @return array<string, mixed> */
    public function updateUser(string $token, string $userId, array $user): array
    {
        return $this->authenticationClient->updateUser($token, $userId, $user);
    }

    /** @return array<string, mixed> */
    public function deleteUser(string $token, string $userId): array
    {
        return $this->authenticationClient->deleteUser($token, $userId);
    }

    /** @return array<string, mixed> */
    public function assignRole(string $token, string $userId, string $role): array
    {
        return $this->authorizationClient->assignRole($token, $userId, $role);
    }

    /** @return array<string, mixed> */
    public function unassignRole(string $token, string $userId, string $role): array
    {
        return $this->authorizationClient->unassignRole($token, $userId, $role);
    }
}
