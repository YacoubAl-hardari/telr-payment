<?php

namespace yacoubalhaidari\Telr\Tests\Unit\DTOs;

use Illuminate\Auth\GenericUser;
use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\CustomerDTO;
use yacoubalhaidari\Telr\DTOs\Order\CustomerNameDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderDetailsDTO;
use yacoubalhaidari\Telr\DTOs\Order\ReturnUrlsDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;
use yacoubalhaidari\Telr\Tests\TestCase;

class CreateOrderDTOTest extends TestCase
{
    public function test_it_builds_the_expected_payload_shape(): void
    {
        $dto = new CreateOrderDTO(
            order: new OrderDetailsDTO(
                cartId: '12324',
                amount: '10.50',
                currency: Currency::AED,
                description: 'My purchase',
                test: true,
            ),
            return: new ReturnUrlsDTO(
                authorised: 'https://example.com/authorised',
                declined: 'https://example.com/declined',
                cancelled: 'https://example.com/cancelled',
            ),
        );

        $payload = $dto->toArray('1234', 'mykey1234');

        $this->assertSame('create', $payload['method']);
        $this->assertSame('1234', $payload['store']);
        $this->assertSame('mykey1234', $payload['authkey']);
        $this->assertSame('12324', $payload['order']['cartid']);
        $this->assertSame('10.50', $payload['order']['amount']);
        $this->assertSame('AED', $payload['order']['currency']);
        $this->assertSame('https://example.com/authorised', $payload['return']['authorised']);
    }

    public function test_it_rejects_a_description_over_63_characters(): void
    {
        $this->expectException(TelrValidationException::class);

        new OrderDetailsDTO(
            cartId: '1',
            amount: '10.50',
            currency: Currency::AED,
            description: str_repeat('a', 64),
        );
    }

    public function test_it_hides_customer_data_when_invoice_data_is_disabled(): void
    {
        config(['telr.show_invoice_data' => false]);

        $dto = new CreateOrderDTO(
            order: new OrderDetailsDTO('1', '10.50', Currency::AED, 'desc'),
            return: new ReturnUrlsDTO('https://a', 'https://b', 'https://c'),
            customer: new CustomerDTO(email: 'buyer@example.test'),
        );

        $this->assertArrayNotHasKey('customer', $dto->toArray('1234', 'key'));
    }

    public function test_it_prefills_customer_data_from_the_authenticated_user_when_enabled(): void
    {
        config(['telr.show_invoice_data' => true]);
        $this->be(new GenericUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.test',
            'country_code' => 'AE',
        ]));

        $dto = new CreateOrderDTO(
            order: new OrderDetailsDTO('1', '10.50', Currency::AED, 'desc'),
            return: new ReturnUrlsDTO('https://a', 'https://b', 'https://c'),
        );

        $customer = $dto->toArray('1234', 'key')['customer'];

        $this->assertSame('jane@example.test', $customer['email']);
        $this->assertSame('Jane', $customer['name']['forenames']);
        $this->assertSame('Doe', $customer['name']['surname']);
        $this->assertSame('AE', $customer['address']['country']);
    }

    public function test_it_sends_explicit_customer_data_when_invoice_data_is_enabled(): void
    {
        config(['telr.show_invoice_data' => true]);

        $dto = new CreateOrderDTO(
            order: new OrderDetailsDTO('1', '10.50', Currency::AED, 'desc'),
            return: new ReturnUrlsDTO('https://a', 'https://b', 'https://c'),
            customer: new CustomerDTO(
                email: 'buyer@example.test',
                name: new CustomerNameDTO('Buyer', 'Example'),
            ),
        );

        $customer = $dto->toArray('1234', 'key')['customer'];

        $this->assertSame('buyer@example.test', $customer['email']);
        $this->assertSame('Buyer', $customer['name']['forenames']);
    }

    public function test_it_rejects_more_than_two_webhooks(): void
    {
        $this->expectException(TelrValidationException::class);

        new CreateOrderDTO(
            order: new OrderDetailsDTO('1', '10.50', Currency::AED, 'desc'),
            return: new ReturnUrlsDTO('https://a', 'https://b', 'https://c'),
            webhooks: [
                new \yacoubalhaidari\Telr\DTOs\Order\WebhookUrlDTO('https://a.com'),
                new \yacoubalhaidari\Telr\DTOs\Order\WebhookUrlDTO('https://b.com'),
                new \yacoubalhaidari\Telr\DTOs\Order\WebhookUrlDTO('https://c.com'),
            ],
        );
    }
}
