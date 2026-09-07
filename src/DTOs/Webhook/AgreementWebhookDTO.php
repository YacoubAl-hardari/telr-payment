<?php

namespace yacoubalhaidari\Telr\DTOs\Webhook;

use yacoubalhaidari\Telr\Enums\AgreementAction;

/**
 * Typed view of an agreement-related webhook POST body.
 */
final class AgreementWebhookDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $agreementId,
        public readonly ?AgreementAction $action,
        public readonly string $storeId,
        public readonly ?string $changeBy,
        public readonly ?string $timestamp,
        public readonly array $raw,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            type: (string) ($payload['type'] ?? ''),
            agreementId: (string) ($payload['agreement_id'] ?? ''),
            action: isset($payload['action']) ? AgreementAction::tryFrom(strtoupper($payload['action'])) : null,
            storeId: (string) ($payload['store_id'] ?? ''),
            changeBy: $payload['change_by'] ?? null,
            timestamp: $payload['timestamp'] ?? null,
            raw: $payload,
        );
    }
}
