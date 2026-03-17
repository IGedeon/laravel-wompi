<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Supported: "sandbox", "production"
    |
    */

    'environment' => env('WOMPI_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | Set the appropriate keys for your current environment in .env.
    | Sandbox keys use prefixes: pub_test_, prv_test_, test_events_, test_integrity_
    | Production keys use: pub_prod_, prv_prod_, prod_events_, prod_integrity_
    |
    */

    'keys' => [
        'public'    => env('WOMPI_PUBLIC_KEY'),
        'private'   => env('WOMPI_PRIVATE_KEY'),
        'events'    => env('WOMPI_EVENTS_SECRET'),
        'integrity' => env('WOMPI_INTEGRITY_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'path'       => env('WOMPI_WEBHOOK_PATH', 'wompi/webhook'),
        'middleware'  => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('WOMPI_CURRENCY', 'COP'),

];
