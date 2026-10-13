<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Grpc\Clients\AuthenticationClient;
use App\Services\Authorization\PermissionAuthorizationService;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_guest_sees_the_login_screen_and_is_redirected_from_admin_routes(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in to your account');

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_valid_admin_login_stores_the_token_only_in_the_server_session(): void
    {
        $this->mock(AuthenticationClient::class)
            ->shouldReceive('login')
            ->once()
            ->with('admin@example.com', 'correct horse battery staple')
            ->andReturn([
                'access_token' => 'opaque-service-token',
                'user' => [
                    'id' => '7f4a3c8e-03d1-4f0a-b39a-44af0d5ef2a2',
                    'email' => 'admin@example.com',
                    'first_name' => 'Ada',
                    'last_name' => 'Admin',
                    'is_active' => true,
                ],
            ]);

        $this->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->with('opaque-service-token', 'admin.dashboard.view')
            ->andReturn(true);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'correct horse battery staple',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('admin.access_token', 'opaque-service-token');
        $response->assertSessionHas('admin.user.email', 'admin@example.com');
        $response->assertDontSee('opaque-service-token');
    }

    public function test_login_rejects_accounts_without_dashboard_permission_and_clears_the_token(): void
    {
        $authenticationClient = $this->mock(AuthenticationClient::class);
        $authenticationClient
            ->shouldReceive('login')
            ->once()
            ->andReturn([
                'access_token' => 'user-token',
                'user' => ['id' => '7f4a3c8e-03d1-4f0a-b39a-44af0d5ef2a2', 'email' => 'user@example.com', 'is_active' => true],
            ]);
        $authenticationClient
            ->shouldReceive('logout')
            ->once()
            ->with('user-token')
            ->andReturn(['success' => true]);
        $this->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->andReturn(false);

        $response = $this->post(route('login.store'), [
            'email' => 'user@example.com',
            'password' => 'a long enough password',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionMissing('admin.access_token');
    }

    public function test_login_validation_does_not_send_invalid_credentials_to_the_service(): void
    {
        $this->mock(AuthenticationClient::class)->shouldNotReceive('login');

        $response = $this->post(route('login.store'), ['email' => 'not-an-email', 'password' => '']);

        $response->assertSessionHasErrors(['email', 'password']);
        $response->assertSessionMissing('_old_input.password');
    }
}
