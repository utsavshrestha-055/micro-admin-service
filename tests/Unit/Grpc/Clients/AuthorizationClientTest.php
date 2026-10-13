<?php

namespace Tests\Unit\Grpc\Clients;

use App\Grpc\Clients\AuthorizationClient;
use App\Grpc\GrpcurlTransport;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AuthorizationClientTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_role_update_uses_camel_case_proto_json_fields(): void
    {
        $transport = Mockery::mock(GrpcurlTransport::class);
        $transport->shouldReceive('call')
            ->once()
            ->with('AuthorizationService', 'UpdateRole', [
                'accessToken' => 'service-token',
                'roleId' => 'role-id',
                'name' => 'support',
                'description' => 'Support team',
                'isActive' => true,
            ])
            ->andReturn(['role' => ['id' => 'role-id']]);

        $response = (new AuthorizationClient($transport))->updateRole(
            'service-token',
            'role-id',
            'support',
            'Support team',
            true,
        );

        $this->assertSame(['role' => ['id' => 'role-id']], $response);
    }
}
