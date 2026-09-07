<?php

namespace yacoubalhaidari\Telr;

use Illuminate\Support\ServiceProvider;
use yacoubalhaidari\Telr\Contracts\AgreementInterface;
use yacoubalhaidari\Telr\Contracts\InvoiceInterface;
use yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface;
use yacoubalhaidari\Telr\Contracts\QuickLinkInterface;
use yacoubalhaidari\Telr\Contracts\WebhookVerifierInterface;
use yacoubalhaidari\Telr\Services\AgreementService;
use yacoubalhaidari\Telr\Services\HttpClient;
use yacoubalhaidari\Telr\Services\InvoiceService;
use yacoubalhaidari\Telr\Services\PaymentPageService;
use yacoubalhaidari\Telr\Services\QuickLinkService;
use yacoubalhaidari\Telr\Services\ServiceApiService;
use yacoubalhaidari\Telr\Services\WebhookService;
use yacoubalhaidari\Telr\Support\SignatureGenerator;

class TelrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/telr.php', 'telr');

        $this->app->singleton(HttpClient::class, fn ($app) => new HttpClient(
            baseUrl: config('telr.base_url', 'https://secure.telr.com'),
            timeout: (int) config('telr.timeout', 30),
        ));

        $this->app->singleton(SignatureGenerator::class, fn ($app) => new SignatureGenerator(
            secretKey: (string) config('telr.secret_key'),
        ));

        $this->app->singleton(PaymentGatewayInterface::class, fn ($app) => new PaymentPageService(
            $app->make(HttpClient::class),
            (string) config('telr.store_id'),
            (string) config('telr.auth_key'),
        ));
        $this->app->alias(PaymentGatewayInterface::class, PaymentPageService::class);

        $this->app->singleton(QuickLinkService::class, fn ($app) => new QuickLinkService(
            $app->make(HttpClient::class),
            (string) config('telr.store_id'),
            (string) config('telr.auth_key'),
        ));
        $this->app->bind(QuickLinkInterface::class, QuickLinkService::class);

        $this->app->singleton(InvoiceService::class, fn ($app) => new InvoiceService(
            $app->make(HttpClient::class),
            (string) config('telr.store_id'),
            (string) config('telr.invoice_password'),
        ));
        $this->app->bind(InvoiceInterface::class, InvoiceService::class);

        $this->app->singleton(AgreementService::class, fn ($app) => new AgreementService(
            $app->make(HttpClient::class),
            (string) config('telr.store_id'),
            (string) config('telr.auth_key'),
        ));
        $this->app->bind(AgreementInterface::class, AgreementService::class);

        $this->app->singleton(WebhookService::class, fn ($app) => new WebhookService(
            $app->make(SignatureGenerator::class),
        ));
        $this->app->bind(WebhookVerifierInterface::class, WebhookService::class);

        $this->app->singleton(ServiceApiService::class, function ($app) {
            $merchantId = config('telr.service_api.merchant_id');
            $apiKey = config('telr.service_api.api_key');

            if (!$merchantId || !$apiKey) {
                return null;
            }

            return new ServiceApiService($app->make(HttpClient::class), $merchantId, $apiKey);
        });

        $this->app->singleton('telr', fn ($app) => new Telr(
            $app->make(PaymentGatewayInterface::class),
            $app->make(QuickLinkService::class),
            $app->make(InvoiceService::class),
            $app->make(AgreementService::class),
            $app->make(WebhookService::class),
            $app->make(ServiceApiService::class),
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/telr.php' => config_path('telr.php'),
            ], 'telr-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'telr-migrations');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/webhook.php');
    }
}
