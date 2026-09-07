<?php

namespace yacoubalhaidari\Telr\DTOs\Agreement;

/**
 * Read-only projection of a single row returned by failedAgreement.json.
 */
final class FailedAgreementItemDTO
{
    public function __construct(
        public readonly int $agreementId,
        public readonly int $storeId,
        public readonly string $currencyCode,
        public readonly string $regularPayment,
        public readonly ?string $agreementFailedDate,
        public readonly ?string $reasonForFailure,
        public readonly ?string $billEmail,
        public readonly ?string $paymentType,
        public readonly ?string $paymentInfo,
        public readonly array $raw = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            agreementId: (int) ($data['AgreementID'] ?? 0),
            storeId: (int) ($data['StoreID'] ?? 0),
            currencyCode: (string) ($data['CurrencyCode'] ?? ''),
            regularPayment: (string) ($data['RegularPayment'] ?? '0'),
            agreementFailedDate: $data['AgreementFailedDate'] ?? null,
            reasonForFailure: $data['ReasonForFailure'] ?? null,
            billEmail: $data['BillEmail'] ?? null,
            paymentType: $data['PaymentType'] ?? null,
            paymentInfo: $data['PaymentInfo'] ?? null,
            raw: $data,
        );
    }
}
