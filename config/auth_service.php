<?php

return [
    'host' => env('AUTH_GRPC_HOST', ''),
    'grpcurl_binary' => env('GRPCURL_BINARY', 'grpcurl'),
    'timeout_seconds' => (float) env('AUTH_GRPC_TIMEOUT', 5),
    'connect_timeout_seconds' => (float) env('AUTH_GRPC_CONNECT_TIMEOUT', 2),
    'proto_directory' => base_path('proto'),
    'proto_file' => base_path('proto/auth.proto'),
    'tls' => [
        'enabled' => (bool) env('AUTH_GRPC_TLS', true),
        'ca_certificate' => env('AUTH_GRPC_CA_CERT'),
        'client_certificate' => env('AUTH_GRPC_CLIENT_CERT'),
        'client_key' => env('AUTH_GRPC_CLIENT_KEY'),
    ],
];
