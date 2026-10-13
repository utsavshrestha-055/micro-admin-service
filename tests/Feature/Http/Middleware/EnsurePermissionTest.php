<?php

namespace Tests\Feature\Http\Middleware;

use App\Grpc\GrpcRequestException;
use App\Services\Auth\SessionService;
use App\Services\Authorization\PermissionAuthorizationService;
use Tests\TestCase;

class EnsurePermissionTest extends TestCase
{
    public function test_authorized_admin_sees_live_service_status_and_unfabricated_dashboard(): void
    {
        $this->withSession(['admin.access_token' => 'session-token', 'admin.user' => ['first_name' => 'Ada']]);
        $this->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->with('session-token', 'admin.dashboard.view')
            ->andReturn(true);
        $this->mock(SessionService::class)
            ->shouldReceive('healthCheck')
            ->once()
            ->andReturn(['status' => 'ok']);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Commerce overview')
            ->assertSee('Auth service connected')
            ->assertSee('No commerce data connected');
    }

    public function test_authenticated_user_without_required_permission_gets_forbidden(): void
    {
        $this->withSession(['admin.access_token' => 'session-token'])
            ->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->with('session-token', 'admin.dashboard.view')
            ->andReturn(false);

        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_authorization_service_failure_fails_closed_with_service_unavailable(): void
    {
        $this->withSession(['admin.access_token' => 'session-token'])
            ->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->andThrow(new GrpcRequestException('UNAVAILABLE'));

        $this->get(route('admin.dashboard'))->assertServiceUnavailable();
    }

    public function test_expired_service_session_is_cleared_and_redirected_to_login(): void
    {
        $this->withSession(['admin.access_token' => 'expired-token', 'admin.user' => ['email' => 'admin@example.com']])
            ->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->andThrow(new GrpcRequestException('UNAUTHENTICATED'));

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('admin.access_token');
    }
}
