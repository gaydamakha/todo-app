<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'session' => [
            // Session cookie settings
            'name'           => 'slim_session',
            'lifetime'       => 60,
            'path'           => '/',
            'domain'         => null,
            'secure'         => false,
            'httponly'       => true,

            // Set session cookie path, domain and secure automatically
            'cookie_autoset' => true,

            // Path where session files are stored, PHP's default path will be used if set null
            'save_path'      => null,

            // Session cache limiter
            'cache_limiter'  => 'nocache',

            // Extend session lifetime after each user activity
            'autorefresh'    => false,

            // Encrypt session data if string is set
            'encryption_key' => null,

            // Session namespace
            'namespace'      => 'slim_app'
        ]
    ]
];