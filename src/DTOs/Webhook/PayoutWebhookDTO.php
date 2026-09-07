<?php

namespace yacoubalhaidari\Telr\DTOs\Webhook;

/**
 * Typed view of a payout webhook POST body (type=ACCOUNT, action=PAYOUT).
 */
final class PayoutWebhookDTO
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $storeId,
        public readonly string $payoutId,
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?string $date,
        public readonly ?string $payoutInterval,
        public readonly ?string $payoutFee,
        public readonly array $raw,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            accountId: (string) ($payload['account_id'] ?? ''),
            storeId: (string) ($payload['store_id'] ?? ''),
            payoutId: (string) ($payload['payout_id'] ?? ''),
            amount: (string) ($payload['amount'] ?? '0'),
            currency: (string) ($payload['currency'] ?? ''),
            date: $payload['date'] ?? null,
            payoutInterval: $payload['payout_interval'] ?? null,
            payoutFee: $payload['payout_fee'] ?? null,
            raw: $payload,
        );
    }
}
