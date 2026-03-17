<?php

namespace IGedeon\WompiLaravel;

use IGedeon\WompiLaravel\Http\Middleware\VerifyWebhookSignature;
use IGedeon\WompiLaravel\Http\WompiClient;
use IGedeon\WompiLaravel\Services\IntegritySignatureService;
use IGedeon\WompiLaravel\Services\MerchantService;
use IGedeon\WompiLaravel\Services\PaymentLinkService;
use IGedeon\WompiLaravel\Services\TransactionService;
use IGedeon\WompiLaravel\Services\WebhookSignatureService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WompiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wompi.php', 'wompi');

        $this->app->singleton(WompiClient::class, fn ($app) => new WompiClient($app['config']['wompi']));

        $this->app->singleton(IntegritySignatureService::class, fn ($app) => new IntegritySignatureService($app['config']['wompi']));

        $this->app->singleton(WebhookSignatureService::class, fn ($app) => new WebhookSignatureService($app['config']['wompi']));

        $this->app->singleton(PaymentLinkService::class);
        $this->app->singleton(TransactionService::class);
        $this->app->singleton(MerchantService::class);
        $this->app->singleton(Wompi::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/wompi.php' => config_path('wompi.php'),
        ], 'wompi-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/webhooks.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wompi');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/wompi'),
        ], 'wompi-views');

        Blade::componentNamespace('IGedeon\\WompiLaravel\\View\\Components', 'wompi');

        $this->app['router']->aliasMiddleware('wompi.verify-signature', VerifyWebhookSignature::class);
    }
}
