<?php

namespace App\Services\Users;

use App\Grpc\Clients\AuthenticationClient;
use App\Grpc\Clients\AuthorizationClient;
use Illuminate\Support\Facades\Log;
use RuntimeException;

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
    public function createUser(string $token, string $email, string $password, string $firstName, string $lastName): array
    {
        $response = $this->authenticationClient->register($email, $password, $firstName, $lastName);
        $createdUser = $response['user'] ?? null;
        $createdUserToken = $response['access_token'] ?? null;

        if (! is_string($createdUserToken) || $createdUserToken === '') {
            throw new RuntimeException('The authentication service returned an invalid registration session.');
        }

        if (! is_array($createdUser) || ! isset($createdUser['id'], $createdUser['email'])) {
            $this->revokeCreatedUserSession($createdUserToken);

            throw new RuntimeException('The authentication service returned an invalid registration response.');
        }

        try {
            $this->authorizationClient->assignRole($token, (string) $createdUser['id'], 'customer');
            $assignedRoles = $this->authorizationClient->listUserRoles($token, (string) $createdUser['id'])['values'] ?? [];

            foreach ($assignedRoles as $role) {
                if (! is_string($role) || $role === 'customer') {
                    continue;
                }

                $this->authorizationClient->unassignRole($token, (string) $createdUser['id'], $role);
            }
        } catch (RuntimeException $exception) {
            throw new RuntimeException(
                'The account was created, but customer access could not be assigned. Synchronize customer access and repair the account roles before retrying.',
                previous: $exception,
            );
        } finally {
            $this->revokeCreatedUserSession($createdUserToken);
        }

        return $createdUser;
    }

    private function revokeCreatedUserSession(string $token): void
    {
        try {
            $this->authenticationClient->logout($token);
        } catch (RuntimeException $exception) {
            Log::notice('The newly created user session could not be revoked.', [
                'reason' => class_basename($exception),
            ]);
        }
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
