<?php

namespace IGedeon\WompiLaravel\Tests;

use IGedeon\WompiLaravel\Facades\Wompi;
use IGedeon\WompiLaravel\WompiServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [WompiServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Wompi' => Wompi::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('wompi.environment', 'sandbox');
        $app['config']->set('wompi.keys.public', 'pub_test_fake_key');
        $app['config']->set('wompi.keys.private', 'prv_test_fake_key');
        $app['config']->set('wompi.keys.events', 'test_events_fake_key');
        $app['config']->set('wompi.keys.integrity', 'test_integrity_fake_key');
    }
}
