<?php

namespace App\Grpc;

use RuntimeException;

class GrpcRequestException extends RuntimeException
{
    public function __construct(public readonly string $grpcStatus, string $message = 'The authentication service request failed.')
    {
        parent::__construct($message);
    }
}
