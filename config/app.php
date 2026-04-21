<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Intranet',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => (bool) (int) (getenv('APP_DEBUG') ?: 0),
    'url' => getenv('APP_URL') ?: 'http://localhost:8080',
    'session_name' => getenv('SESSION_NAME') ?: 'intranet_prompt',
];
