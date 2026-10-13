<?php

namespace Tests\Unit\Grpc;

use App\Grpc\ProtobufJsonMapper;
use PHPUnit\Framework\TestCase;

class ProtobufJsonMapperTest extends TestCase
{
    public function test_normalizes_nested_protobuf_json_response_keys(): void
    {
        $mapper = new ProtobufJsonMapper;

        $response = $mapper->normalizeResponse([
            'isActive' => true,
            'rolePermissions' => [
                ['permissionId' => 'permission-1', 'createdAt' => '2025-01-01T00:00:00Z'],
            ],
        ]);

        $this->assertSame([
            'is_active' => true,
            'role_permissions' => [
                ['permission_id' => 'permission-1', 'created_at' => '2025-01-01T00:00:00Z'],
            ],
        ], $response);
    }
}
