<?php

namespace App\Services\Auth;

use App\Grpc\Clients\AuthenticationClient;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SessionService
{
    public function __construct(private AuthenticationClient $authenticationClient) {}

    /** @return array<string, mixed> */
    public function healthCheck(): array
    {
        return $this->authenticationClient->healthCheck();
    }

    /** @return array<string, mixed> */
    public function authenticate(string $email, string $password): array
    {
        $response = $this->authenticationClient->login($email, $password);
        $user = $response['user'] ?? null;
        $token = $response['access_token'] ?? null;

        if (! is_array($user) || ! is_string($token) || $token === '' || ! isset($user['id'], $user['email'])) {
            throw new RuntimeException('The authentication service returned an invalid login response.');
        }

        session()->regenerate();
        session()->put('admin.access_token', $token);
        session()->put('admin.user', [
            'id' => (string) $user['id'],
            'email' => (string) $user['email'],
            'first_name' => (string) ($user['first_name'] ?? ''),
            'last_name' => (string) ($user['last_name'] ?? ''),
            'is_active' => (bool) ($user['is_active'] ?? false),
        ]);

        return session('admin.user');
    }

    public function logout(): bool
    {
        $token = session('admin.access_token');
        $loggedOut = false;

        try {
            if (is_string($token) && $token !== '') {
                $response = $this->authenticationClient->logout($token);
                $loggedOut = (bool) ($response['success'] ?? false);
            }
        } catch (RuntimeException $exception) {
            Log::notice('Remote logout failed; the local admin session was cleared.', [
                'reason' => class_basename($exception),
            ]);
        } finally {
            session()->invalidate();
            session()->regenerateToken();
        }

        return $loggedOut;
    }
}
