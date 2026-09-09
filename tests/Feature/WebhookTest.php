<?php

namespace yacoubalhaidari\Telr\Tests\Feature;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use yacoubalhaidari\Telr\Events\TelrTransactionAuthorised;
use yacoubalhaidari\Telr\Events\TelrTransactionDeclined;
use yacoubalhaidari\Telr\Tests\TestCase;

class WebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([TelrTransactionAuthorised::class, TelrTransactionDeclined::class]);
    }

    public function test_authorised_transaction_dispatches_exactly_one_authorised_event(): void
    {
        $payload = $this->signedTransaction();

        $this->postJson('/webhooks/telr', $payload)->assertOk()->assertContent('OK');

        Event::assertDispatchedTimes(TelrTransactionAuthorised::class, 1);
        Event::assertDispatched(TelrTransactionAuthorised::class, function ($event) use ($payload): bool {
            return $event->transaction->tranRef === '040035754014'
                && $event->transaction->amount === '10.50'
                && $event->transaction->orderRef === null
                && $event->transaction->isAuthorised()
                && $event->transaction->raw === $payload;
        });
        Event::assertNotDispatched(TelrTransactionDeclined::class);
    }

    #[DataProvider('declinedStatuses')]
    public function test_declined_transaction_dispatches_exactly_one_declined_event(string $status): void
    {
        $payload = $this->signedTransaction(['tran_status' => $status, 'tran_authmessage' => 'Not authorised']);

        $this->postJson('/webhooks/telr', $payload)->assertOk();

        Event::assertDispatchedTimes(TelrTransactionDeclined::class, 1);
        Event::assertDispatched(TelrTransactionDeclined::class, function ($event) use ($status): bool {
            return $event->transaction->status?->value === $status
                && ! $event->transaction->isAuthorised();
        });
        Event::assertNotDispatched(TelrTransactionAuthorised::class);
    }

    public static function declinedStatuses(): array
    {
        return [
            'declined' => ['D'],
            'cancelled' => ['C'],
            'error' => ['E'],
            'expired' => ['X'],
        ];
    }

    public function test_optional_order_reference_is_verified_and_included_in_the_event(): void
    {
        $payload = $this->signedTransaction(['tran_order' => 'ORDER-123']);

        $this->postJson('/webhooks/telr', $payload)->assertOk();

        Event::assertDispatchedTimes(TelrTransactionAuthorised::class, 1);
        Event::assertDispatched(TelrTransactionAuthorised::class, fn ($event): bool => $event->transaction->orderRef === 'ORDER-123');
        Event::assertNotDispatched(TelrTransactionDeclined::class);
    }

    public function test_form_decoding_preserves_literal_plus_signs_and_percent_sequences(): void
    {
        $payload = $this->signedTransaction([
            'tran_cartid' => 'cart+A%2B+B',
            'tran_desc' => 'C++ + 10% discount %2B %25',
            'tran_order' => 'ORDER+%2B',
        ]);
        $encodedBody = http_build_query($payload, '', '&', PHP_QUERY_RFC1738);
        $this->assertStringContainsString('tran_cartid=cart%2BA%252B%2BB', $encodedBody);

        // PHP populates POST parameters by decoding form input once before the
        // Laravel request reaches the controller. Reproduce that boundary here.
        parse_str($encodedBody, $decodedPayload);
        $this->assertSame($payload, $decodedPayload);

        $this->call('POST', '/webhooks/telr', $decodedPayload, [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ], $encodedBody)->assertOk();

        Event::assertDispatchedTimes(TelrTransactionAuthorised::class, 1);
        Event::assertDispatched(TelrTransactionAuthorised::class, function ($event) use ($payload): bool {
            return $event->transaction->cartId === $payload['tran_cartid']
                && $event->transaction->description === $payload['tran_desc']
                && $event->transaction->orderRef === $payload['tran_order'];
        });
        Event::assertNotDispatched(TelrTransactionDeclined::class);
    }

    #[DataProvider('tamperedFields')]
    public function test_tampered_signed_fields_are_acknowledged_without_dispatching_events(array $changes): void
    {
        $payload = array_replace($this->signedTransaction(['tran_order' => 'ORDER-123']), $changes);

        $this->postJson('/webhooks/telr', $payload)->assertOk()->assertContent('OK');

        Event::assertNothingDispatched();
    }

    public static function tamperedFields(): array
    {
        return [
            'amount' => [['tran_amount' => '0.01']],
            'currency' => [['tran_currency' => 'SAR']],
            'merchant' => [['tran_store' => 'different-store']],
            'transaction reference' => [['tran_ref' => 'different-reference']],
            'cart' => [['tran_cartid' => 'different-cart']],
            'order reference' => [['tran_order' => 'different-order']],
            'status' => [['tran_status' => 'D']],
            'array amount' => [['tran_amount' => ['10.50']]],
            'array order reference' => [['tran_order' => ['ORDER-123']]],
            'array status' => [['tran_status' => ['A']]],
        ];
    }

    #[DataProvider('malformedSignatures')]
    public function test_malformed_signatures_are_acknowledged_without_dispatching_events(mixed $signature): void
    {
        $payload = $this->signedTransaction();
        $payload['tran_check'] = $signature;

        $this->postJson('/webhooks/telr', $payload)->assertOk()->assertContent('OK');

        Event::assertNothingDispatched();
    }

    public static function malformedSignatures(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'too short' => [str_repeat('a', 39)],
            'too long' => [str_repeat('a', 41)],
            'non hex' => [str_repeat('z', 40)],
            'integer' => [1234],
            'boolean' => [true],
            'empty array' => [[]],
            'array of hashes' => [[str_repeat('a', 40)]],
        ];
    }

    public function test_missing_signature_does_not_dispatch_an_event(): void
    {
        $payload = $this->signedTransaction();
        unset($payload['tran_check']);

        $this->postJson('/webhooks/telr', $payload)->assertOk();

        Event::assertNothingDispatched();
    }

    public function test_empty_configured_secret_rejects_even_a_matching_empty_key_signature(): void
    {
        config(['telr.secret_key' => '']);
        $payload = $this->signedTransaction([], '');

        $this->postJson('/webhooks/telr', $payload)->assertOk();

        Event::assertNothingDispatched();
    }

    private function signedTransaction(array $overrides = [], string $secretKey = 'test-secret-key'): array
    {
        $payload = array_replace([
            'tran_store' => '1234',
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'tran_test' => '1',
            'tran_ref' => '040035754014',
            'tran_prevref' => '040035754014',
            'tran_firstref' => '040035754014',
            'tran_currency' => 'AED',
            'tran_amount' => '10.50',
            'tran_cartid' => '12324',
            'tran_desc' => 'My purchase',
            'tran_status' => 'A',
            'tran_authcode' => '078251',
            'tran_authmessage' => 'Authorised',
        ], $overrides);

        // Independent fixture: concatenate the documented wire-field order,
        // including the optional order reference before currency.
        $values = [
            $secretKey,
            $payload['tran_store'],
            $payload['tran_type'],
            $payload['tran_class'],
            $payload['tran_test'],
            $payload['tran_ref'],
            $payload['tran_prevref'],
            $payload['tran_firstref'],
        ];
        if (array_key_exists('tran_order', $payload)) {
            $values[] = $payload['tran_order'];
        }
        $payload['tran_check'] = sha1(implode(':', array_merge($values, [
            $payload['tran_currency'],
            $payload['tran_amount'],
            $payload['tran_cartid'],
            $payload['tran_desc'],
            $payload['tran_status'],
            $payload['tran_authcode'],
            $payload['tran_authmessage'],
        ])));

        return $payload;
    }
}
