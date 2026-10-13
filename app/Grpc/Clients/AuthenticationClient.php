<?php

namespace App\Grpc\Clients;

use App\Grpc\GrpcTransport;

class AuthenticationClient
{
    public function __construct(private GrpcTransport $transport) {}

    /** @return array<string, mixed> */
    public function login(string $email, string $password): array
    {
        return $this->transport->call('AuthenticationService', 'Login', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    /** @return array<string, mixed> */
    public function healthCheck(): array
    {
        return $this->transport->call('AuthenticationService', 'HealthCheck', []);
    }

    /** @return array<string, mixed> */
    public function logout(string $accessToken): array
    {
        return $this->transport->call('AuthenticationService', 'Logout', [
            'accessToken' => $accessToken,
        ]);
    }

    /** @return array<string, mixed> */
    public function getUser(string $accessToken, string $userId): array
    {
        return $this->transport->call('AuthenticationService', 'GetUser', [
            'accessToken' => $accessToken,
            'userId' => $userId,
        ]);
    }

    /** @return array<string, mixed> */
    public function listUsers(string $accessToken, int $limit, int $offset): array
    {
        return $this->transport->call('AuthenticationService', 'ListUsers', [
            'accessToken' => $accessToken,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /** @param array<string, mixed> $user @return array<string, mixed> */
    public function updateUser(string $accessToken, string $userId, array $user): array
    {
        return $this->transport->call('AuthenticationService', 'UpdateUser', [
            'accessToken' => $accessToken,
            'userId' => $userId,
            'email' => $user['email'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'isActive' => $user['is_active'],
            'password' => $user['password'] ?? '',
        ]);
    }

    /** @return array<string, mixed> */
    public function deleteUser(string $accessToken, string $userId): array
    {
        return $this->transport->call('AuthenticationService', 'DeleteUser', [
            'accessToken' => $accessToken,
            'userId' => $userId,
        ]);
    }
}
