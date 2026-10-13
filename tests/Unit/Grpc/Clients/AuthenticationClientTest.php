<?php

namespace Tests\Unit\Grpc\Clients;

use App\Grpc\Clients\AuthenticationClient;
use App\Grpc\GrpcurlTransport;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AuthenticationClientTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_login_maps_the_verified_proto_method_and_fields(): void
    {
        $transport = Mockery::mock(GrpcurlTransport::class);
        $transport->shouldReceive('call')
            ->once()
            ->with('AuthenticationService', 'Login', [
                'email' => 'admin@example.com',
                'password' => 'secret',
            ])
            ->andReturn(['access_token' => 'service-token']);

        $response = (new AuthenticationClient($transport))->login('admin@example.com', 'secret');

        $this->assertSame(['access_token' => 'service-token'], $response);
    }
}
