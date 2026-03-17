<?php

use IGedeon\WompiLaravel\Http\Controllers\WebhookController;
use IGedeon\WompiLaravel\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Support\Facades\Route;

Route::post(config('wompi.webhook.path', 'wompi/webhook'), WebhookController::class)
    ->middleware(array_merge(
        [VerifyWebhookSignature::class],
        config('wompi.webhook.middleware', [])
    ))
    ->name('wompi.webhook');
