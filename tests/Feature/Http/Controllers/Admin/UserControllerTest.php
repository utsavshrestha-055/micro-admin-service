<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Services\Authorization\PermissionAuthorizationService;
use App\Services\Users\UserManagementService;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_user_directory_renders_the_service_page_without_database_records(): void
    {
        $this->withSession(['admin.access_token' => 'session-token']);
        $this->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->with('session-token', 'users.read')
            ->andReturn(true);
        $this->mock(UserManagementService::class)
            ->shouldReceive('listUsers')
            ->once()
            ->with('session-token', 26, 0)
            ->andReturn(['users' => [[
                'id' => '7f4a3c8e-03d1-4f0a-b39a-44af0d5ef2a2',
                'email' => 'ada@example.com',
                'first_name' => 'Ada',
                'last_name' => 'Admin',
                'is_active' => true,
                'created_at' => '2025-01-01T00:00:00Z',
            ]]]);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('ada@example.com')
            ->assertSee('Active');
    }

    public function test_invalid_user_email_is_rejected_before_the_service_update(): void
    {
        $userId = '7f4a3c8e-03d1-4f0a-b39a-44af0d5ef2a2';

        $this->withSession(['admin.access_token' => 'session-token'])
            ->mock(PermissionAuthorizationService::class)
            ->shouldReceive('allows')
            ->once()
            ->with('session-token', 'users.manage')
            ->andReturn(true);
        $this->mock(UserManagementService::class)->shouldNotReceive('updateUser');

        $this->put(route('admin.users.update', $userId), [
            'email' => 'not-an-email',
            'first_name' => 'Ada',
            'last_name' => 'Admin',
            'is_active' => '1',
            'password' => '',
        ])->assertSessionHasErrors('email');
    }

    public function test_role_assignment_rejects_a_role_name_not_returned_by_the_service(): void
    {
        $userId = '7f4a3c8e-03d1-4f0a-b39a-44af0d5ef2a2';
        $permissionAuthorizationService = $this->mock(PermissionAuthorizationService::class);
        $permissionAuthorizationService->shouldReceive('allows')
            ->times(3)
            ->andReturn(true);
        $userManagementService = $this->mock(UserManagementService::class);
        $userManagementService->shouldReceive('listRoles')->once()->andReturn([
            'roles' => [['id' => '0d0ff60c-aa01-4faa-ad47-580779a1229e', 'name' => 'operator']],
        ]);
        $userManagementService->shouldNotReceive('assignRole');

        $this->withSession(['admin.access_token' => 'session-token'])
            ->post(route('admin.users.roles.assign', $userId), ['role' => 'superadmin'])
            ->assertSessionHasErrors('role');
    }
}
