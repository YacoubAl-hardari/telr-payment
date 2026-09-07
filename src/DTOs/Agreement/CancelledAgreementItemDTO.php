<?php

namespace yacoubalhaidari\Telr\DTOs\Agreement;

/**
 * Read-only projection of a single row returned by cancelledAgreement.json.
 */
final class CancelledAgreementItemDTO
{
    public function __construct(
        public readonly int $agreementId,
        public readonly string $agreementStatus,
        public readonly ?string $cancelDate,
        public readonly ?string $description,
        public readonly ?string $customerEmail,
        public readonly ?string $cancelledBy,
        public readonly ?string $reason,
        public readonly ?string $nextDueAmount,
        public readonly array $raw = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            agreementId: (int) ($data['AgreementID'] ?? 0),
            agreementStatus: (string) ($data['AgreementStatus'] ?? ''),
            cancelDate: $data['CancelDate'] ?? null,
            description: $data['Description'] ?? null,
            customerEmail: $data['CustomerEmail'] ?? null,
            cancelledBy: $data['CancelledBy'] ?? null,
            reason: $data['Reason'] ?? null,
            nextDueAmount: $data['NextDueAmount'] ?? null,
            raw: $data,
        );
    }
}
