<?php

return [
    // Enables POST /_ops/deploy (runs migrations and rebuilds caches) for hosts
    // without SSH. Leave empty to disable the endpoint entirely.
    'ops_token' => env('OPS_TOKEN'),

    // Used by the seeder to create the first admin account.
    'admin_email' => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),
];
