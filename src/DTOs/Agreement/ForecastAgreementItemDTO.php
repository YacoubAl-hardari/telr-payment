<?php

namespace yacoubalhaidari\Telr\DTOs\Agreement;

/**
 * Read-only projection of a single row returned by forecastagreement.json.
 */
final class ForecastAgreementItemDTO
{
    public function __construct(
        public readonly int $agreementId,
        public readonly int $storeId,
        public readonly string $description,
        public readonly string $currency,
        public readonly string $nextDueAmount,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $nextDueDate,
        public readonly ?string $actPayDate,
        public readonly ?string $paymentType,
        public readonly ?string $paymentInfo,
        public readonly ?string $lastTranStatus,
        public readonly array $raw = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            agreementId: (int) ($data['AgreementID'] ?? 0),
            storeId: (int) ($data['StoreID'] ?? 0),
            description: (string) ($data['Description'] ?? ''),
            currency: (string) ($data['Currency'] ?? ''),
            nextDueAmount: (string) ($data['NextDueAmount'] ?? '0'),
            name: (string) ($data['Name'] ?? ''),
            email: $data['Email'] ?? null,
            phone: $data['Phone'] ?? null,
            nextDueDate: $data['NextDueDate'] ?? null,
            actPayDate: $data['ActPayDate'] ?? null,
            paymentType: $data['PaymentType'] ?? null,
            paymentInfo: $data['PaymentInfo'] ?? null,
            lastTranStatus: $data['LastTranStatus'] ?? null,
            raw: $data,
        );
    }
}
