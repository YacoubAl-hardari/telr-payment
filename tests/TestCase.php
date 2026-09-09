<?php

namespace yacoubalhaidari\Telr\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Support\Facades\Http;
use yacoubalhaidari\Telr\Facades\Telr;
use yacoubalhaidari\Telr\TelrServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app): array
    {
        return [TelrServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Telr' => Telr::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('telr.store_id', '12345');
        $app['config']->set('telr.auth_key', 'test-auth-key');
        $app['config']->set('telr.secret_key', 'test-secret-key');
        $app['config']->set('telr.invoice_password', 'test-invoice-password');
        $app['config']->set('telr.service_api.merchant_id', null);
        $app['config']->set('telr.service_api.api_key', null);
    }
}
