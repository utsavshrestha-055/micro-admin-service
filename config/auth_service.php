<?php

return [
    'host' => env('AUTH_GRPC_HOST', ''),
    'timeout_seconds' => (float) env('AUTH_GRPC_TIMEOUT', 5),
    'proto_file' => env('AUTH_GRPC_PROTO_FILE', 'proto/auth.proto'),
    'tls' => [
        'enabled' => (bool) env('AUTH_GRPC_TLS', true),
        'ca_certificate' => env('AUTH_GRPC_CA_CERT'),
        'client_certificate' => env('AUTH_GRPC_CLIENT_CERT'),
        'client_key' => env('AUTH_GRPC_CLIENT_KEY'),
    ],
];
