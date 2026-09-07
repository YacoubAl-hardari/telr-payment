<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\BillingPeriod;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Recurring billing configuration for a QuickLink. Not compatible with
 * an AgreementID-linked QuickLink. Exactly one of $status / $auto
 * should be set true, matching the API's mutually-exclusive fields.
 */
final class RepeatBillingDTO extends BaseDTO
{
    public function __construct(
        public readonly bool $status = false,
        public readonly bool $auto = false,
        public readonly string $type = 'recurring', // 'recurring' | 'unscheduled'
        public readonly ?string $amount = null,
        public readonly ?BillingPeriod $period = null,
        public readonly ?int $interval = null,
        public readonly ?int $start = null,
        public readonly int $term = 0, // 0-100, 0 = unlimited
        public readonly ?string $final = null,
    ) {
        if ($this->status && $this->auto) {
            throw new TelrValidationException('RepeatBilling.Status and RepeatBilling.Auto are mutually exclusive; set exactly one.');
        }

        if ($this->type === 'unscheduled' && $this->auto) {
            throw new TelrValidationException('RepeatBilling.Type "unscheduled" is only valid when Auto is not set.');
        }

        if ($this->term < 0 || $this->term > 100) {
            throw new TelrValidationException('RepeatBilling.Term must be between 0 and 100.');
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'Status' => $this->status ?: null,
            'Auto' => $this->auto ?: null,
            'Type' => $this->type,
            'Amount' => $this->amount,
            'Period' => $this->period?->value,
            'Interval' => $this->interval,
            'Start' => $this->start,
            'Term' => $this->term,
            'Final' => $this->final,
        ]);
    }
}
