<?php

return [
    'paths' => ['*', 'sanctum/csrf-cookie', 'login', '_csrf-debug', 'logout', 'api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://127.0.0.1:5173', 'http://localhost:5173', 'https://dev-hmr.safiradmin.com'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
