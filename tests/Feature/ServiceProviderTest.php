<?php

namespace yacoubalhaidari\Telr\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use yacoubalhaidari\Telr\Contracts\AgreementInterface;
use yacoubalhaidari\Telr\Contracts\InvoiceInterface;
use yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface;
use yacoubalhaidari\Telr\Contracts\QuickLinkInterface;
use yacoubalhaidari\Telr\Contracts\WebhookVerifierInterface;
use yacoubalhaidari\Telr\DTOs\Invoice\CreateInvoiceDTO;
use yacoubalhaidari\Telr\DTOs\Invoice\InvoiceRecipientDTO;
use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderDetailsDTO;
use yacoubalhaidari\Telr\DTOs\Order\ReturnUrlsDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\CreateQuickLinkDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\QuickLinkDetailsDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Enums\InvoiceLayout;
use yacoubalhaidari\Telr\Facades\Telr;
use yacoubalhaidari\Telr\Services\AgreementService;
use yacoubalhaidari\Telr\Services\InvoiceService;
use yacoubalhaidari\Telr\Services\PaymentPageService;
use yacoubalhaidari\Telr\Services\QuickLinkService;
use yacoubalhaidari\Telr\Services\WebhookService;
use yacoubalhaidari\Telr\TelrServiceProvider;
use yacoubalhaidari\Telr\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_discovery_metadata_provider_contracts_facade_and_route_boot(): void
    {
        $manifest = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true);
        $this->assertSame([TelrServiceProvider::class], $manifest['extra']['laravel']['providers']);
        $this->assertSame(Telr::class, $manifest['extra']['laravel']['aliases']['Telr']);
        foreach ([
            PaymentGatewayInterface::class => PaymentPageService::class,
            QuickLinkInterface::class => QuickLinkService::class,
            InvoiceInterface::class => InvoiceService::class,
            AgreementInterface::class => AgreementService::class,
            WebhookVerifierInterface::class => WebhookService::class,
        ] as $contract => $service) {
            $this->assertInstanceOf($service, $this->app->make($contract));
            $this->assertSame($this->app->make($service), $this->app->make($contract));
        }
        $this->assertSame($this->app->make(PaymentGatewayInterface::class), Telr::payments());
        $this->assertSame($this->app->make(WebhookService::class), Telr::webhooks());
        $this->assertTrue(Route::has('telr.webhook'));
        $this->assertSame(['POST'], Route::getRoutes()->getByName('telr.webhook')->methods());
    }

    public function test_optional_service_api_is_not_required_to_resolve_the_payment_facade(): void
    {
        $this->assertInstanceOf(PaymentPageService::class, Telr::payments());
        $this->expectExceptionMessage('Telr Service API credentials are not configured.');
        Telr::serviceApi();
    }

    public function test_hosted_checkout_service_sends_the_existing_authenticated_payload(): void
    {
        Http::fake(['*' => Http::response(['order' => ['ref' => 'order-ref', 'url' => 'https://secure.telr.com/test']])]);
        $response = Telr::createOrder(new CreateOrderDTO(
            new OrderDetailsDTO('test-cart', '12.50', Currency::SAR, 'Test order', test: true),
            new ReturnUrlsDTO('https://example.test/success', 'https://example.test/failure', 'https://example.test/cancel'),
        ));

        $this->assertTrue($response->success);
        $this->assertSame('order-ref', $response->ref);
        Http::assertSent(fn ($request) => $request->url() === 'https://secure.telr.com/gateway/order.json'
            && $request['store'] === '12345' && $request['authkey'] === 'test-auth-key'
            && $request['order']['cartid'] === 'test-cart' && $request['order']['test'] === '1');
        Http::assertSentCount(1);
    }

    public function test_quicklink_service_resolves_and_sends_its_dto(): void
    {
        Http::fake(['*' => Http::response(['QuickLinkResponse' => ['Code' => 200, 'Status' => 'OK', 'URL' => 'https://secure.telr.com/link']])]);
        $response = Telr::quickLinks()->create(new CreateQuickLinkDTO(new QuickLinkDetailsDTO('Test', 'test-cart', Currency::SAR, '12.50')));

        $this->assertTrue($response->success);
        Http::assertSent(fn ($request) => $request->url() === 'https://secure.telr.com/gateway/api_quicklink.json'
            && $request['QuickLinkRequest']['StoreID'] === 12345
            && $request['QuickLinkRequest']['AuthKey'] === 'test-auth-key');
    }

    public function test_invoice_service_serializes_xml_with_invoice_credentials(): void
    {
        Http::fake(['*' => Http::response('<invoice><status>OK</status><reference>invoice-ref</reference><url>https://secure.telr.com/invoice</url></invoice>')]);
        $response = Telr::invoices()->create(new CreateInvoiceDTO(
            'Test invoice', Currency::SAR, '12.50', InvoiceLayout::BASIC, new InvoiceRecipientDTO(email: 'buyer@example.test'),
        ));

        $this->assertTrue($response->success);
        $this->assertSame('invoice-ref', $response->reference);
        Http::assertSent(function ($request) {
            $payload = simplexml_load_string($request->body());
            return $request->url() === 'https://secure.telr.com/gateway/invoice_create.xml'
                && (string) $payload->store === '12345'
                && (string) $payload->password === 'test-invoice-password'
                && (string) $payload->amount === '12.50';
        });
    }
}
