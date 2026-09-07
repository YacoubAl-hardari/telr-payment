<?php

namespace yacoubalhaidari\Telr\DTOs\Webhook;

use yacoubalhaidari\Telr\Enums\TransactionClass;
use yacoubalhaidari\Telr\Enums\TransactionStatus;
use yacoubalhaidari\Telr\Enums\TransactionType;

/**
 * Typed view of a transaction-related webhook POST body, per the
 * "Webhook" documentation page's message-format table.
 */
final class TransactionWebhookDTO
{
    public function __construct(
        public readonly string $store,
        public readonly ?TransactionType $type,
        public readonly ?TransactionClass $class,
        public readonly bool $isTest,
        public readonly string $tranRef,
        public readonly ?string $prevRef,
        public readonly ?string $firstRef,
        public readonly ?string $orderRef,
        public readonly string $currency,
        public readonly string $amount,
        public readonly string $cartId,
        public readonly ?string $description,
        public readonly ?TransactionStatus $status,
        public readonly ?string $authCode,
        public readonly ?string $authMessage,
        public readonly ?string $cardCode,
        public readonly ?string $cardLast4,
        public readonly ?string $billEmail,
        public readonly array $extra,
        public readonly array $raw,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        $extra = [];
        foreach ($payload as $key => $value) {
            if (str_starts_with($key, 'xtra_')) {
                $extra[substr($key, 5)] = $value;
            }
        }

        return new self(
            store: (string) ($payload['tran_store'] ?? ''),
            type: isset($payload['tran_type']) ? TransactionType::tryFrom($payload['tran_type']) : null,
            class: isset($payload['tran_class']) ? TransactionClass::tryFrom($payload['tran_class']) : null,
            isTest: (string) ($payload['tran_test'] ?? '0') === '1',
            tranRef: (string) ($payload['tran_ref'] ?? ''),
            prevRef: $payload['tran_prevref'] ?? null,
            firstRef: $payload['tran_firstref'] ?? null,
            orderRef: $payload['tran_order'] ?? null,
            currency: (string) ($payload['tran_currency'] ?? ''),
            amount: (string) ($payload['tran_amount'] ?? '0'),
            cartId: (string) ($payload['tran_cartid'] ?? ''),
            description: $payload['tran_desc'] ?? null,
            status: isset($payload['tran_status']) ? TransactionStatus::tryFrom($payload['tran_status']) : null,
            authCode: $payload['tran_authcode'] ?? null,
            authMessage: $payload['tran_authmessage'] ?? null,
            cardCode: $payload['card_code'] ?? null,
            cardLast4: $payload['card_last4'] ?? null,
            billEmail: $payload['bill_email'] ?? null,
            extra: $extra,
            raw: $payload,
        );
    }

    public function isAuthorised(): bool
    {
        return $this->status?->isAuthorised() ?? false;
    }
}
