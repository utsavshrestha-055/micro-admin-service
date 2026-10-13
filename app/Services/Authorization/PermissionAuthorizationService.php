<?php

namespace App\Services\Authorization;

use App\Grpc\Clients\AuthorizationClient;

class PermissionAuthorizationService
{
    public function __construct(private AuthorizationClient $authorizationClient) {}

    public function allows(string $token, string $permission): bool
    {
        $response = $this->authorizationClient->checkPermission($token, $permission);

        return ($response['allowed'] ?? false) === true;
    }
}
