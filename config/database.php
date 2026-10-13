<?php

return [
    'default' => env('DB_CONNECTION'),
    'connections' => [
        'sqlite' => null,
        'mysql' => null,
        'mariadb' => null,
        'pgsql' => null,
        'sqlsrv' => null,
    ],
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false,
    ],
];
