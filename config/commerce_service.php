<?php

return [
    'host' => env('COMMERCE_GRPC_HOST', 'micro-product-service.batman:50062'),
    'timeout_seconds' => (float) env('COMMERCE_GRPC_TIMEOUT', 5),
    'tls' => [
        'enabled' => (bool) env('COMMERCE_GRPC_TLS', false),
        'ca_certificate' => env('COMMERCE_GRPC_CA_CERT'),
        'client_certificate' => env('COMMERCE_GRPC_CLIENT_CERT'),
        'client_key' => env('COMMERCE_GRPC_CLIENT_KEY'),
    ],
];
