<?php

namespace yacoubalhaidari\Telr\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use yacoubalhaidari\Telr\DTOs\AuthenticatedRequestDTO;
use yacoubalhaidari\Telr\DTOs\Invoice\CreateInvoiceDTO;
use yacoubalhaidari\Telr\DTOs\Invoice\InvoiceRecipientDTO;
use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderDetailsDTO;
use yacoubalhaidari\Telr\DTOs\Order\ReturnUrlsDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\CreateQuickLinkDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\QuickLinkDetailsDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Enums\InvoiceLayout;

class AuthenticatedRequestDTOTest extends TestCase
{
    public function test_all_package_classes_can_be_autoloaded_without_signature_errors(): void
    {
        $source = dirname(__DIR__, 3).'/src';
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source));
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $relative = substr($file->getPathname(), strlen($source) + 1, -4);
            $class = 'yacoubalhaidari\\Telr\\'.str_replace(['/', '\\'], '\\', $relative);
            $this->assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class), $class);
        }
    }

    public function test_authenticated_request_credentials_remain_required(): void
    {
        foreach ([CreateOrderDTO::class, CreateQuickLinkDTO::class, CreateInvoiceDTO::class] as $class) {
            $this->assertTrue(is_subclass_of($class, AuthenticatedRequestDTO::class));
            $this->assertSame(2, (new ReflectionMethod($class, 'toArray'))->getNumberOfRequiredParameters());
        }
    }

    public function test_order_payload_keeps_existing_named_arguments_and_omits_optional_values(): void
    {
        $dto = new CreateOrderDTO(
            new OrderDetailsDTO('cart', '0.50', Currency::SAR, 'Order', test: false),
            new ReturnUrlsDTO('https://example.test/success', 'https://example.test/failure', 'https://example.test/cancel'),
        );
        $payload = $dto->toArray(store: '12345', authKey: 'test-auth-key');

        $this->assertSame('12345', $payload['store']);
        $this->assertSame('test-auth-key', $payload['authkey']);
        $this->assertSame('0', $payload['order']['test']);
        $this->assertArrayNotHasKey('customer', $payload);
        $this->assertArrayNotHasKey('extra', $payload);
        $this->assertArrayNotHasKey('webhooks', $payload);
    }

    public function test_quicklink_payload_preserves_its_wrapped_credentials(): void
    {
        $dto = new CreateQuickLinkDTO(new QuickLinkDetailsDTO('Link', 'cart', Currency::SAR, '12.50'));
        $payload = $dto->toArray(storeId: '12345', authKey: 'test-auth-key')['QuickLinkRequest'];

        $this->assertSame(12345, $payload['StoreID']);
        $this->assertSame('test-auth-key', $payload['AuthKey']);
        $this->assertSame('12.50', $payload['Details']['Amount']);
        $this->assertArrayNotHasKey('RepeatBilling', $payload);
        $this->assertArrayNotHasKey('Splits', $payload);
    }

    public function test_invoice_payload_keeps_the_dedicated_password_and_nested_recipient(): void
    {
        $dto = new CreateInvoiceDTO('Invoice', Currency::SAR, '12.50', InvoiceLayout::BASIC, new InvoiceRecipientDTO(email: 'buyer@example.test'));
        $payload = $dto->toArray(store: '12345', password: 'test-invoice-password');

        $this->assertSame('12345', $payload['store']);
        $this->assertSame('test-invoice-password', $payload['password']);
        $this->assertSame('buyer@example.test', $payload['recipient']['email']);
        $this->assertSame('0', $payload['testmode']);
        $this->assertArrayNotHasKey('authkey', $payload);
        $this->assertArrayNotHasKey('details', $payload);
        $this->assertArrayNotHasKey('extra', $payload);
    }
}
