<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\BillingPeriod;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;
use yacoubalhaidari\Telr\Support\AmountFormatter;

/**
 * The <repeat> block for remote XML invoice creation. Sets up a repeat
 * billing agreement alongside the invoice.
 *
 * IMPORTANT (per docs): <invoice><amount> is charged immediately when the
 * customer pays this invoice, sent in MAJOR units. <repeat><amount> is
 * charged on every subsequent recurring cycle, and per the XML schema
 * notes must be sent in INTEGER MINOR UNITS. These two fields are never
 * linked automatically — supply both even if they should be equal.
 */
final class InvoiceRepeatDTO extends BaseDTO
{
    public function __construct(
        public readonly string $customerId, // cart ID / reference for the agreement
        public readonly float|string $amountMajorUnits, // recurring charge, major units input
        public readonly BillingPeriod $period,
        public readonly int $interval, // 1-12
        public readonly string $start, // DDMMYYYY or next|first|last|mid
        public readonly ?int $term = null, // 0=unlimited, 1-48=fixed term
        public readonly ?string $final = null, // major units
        public readonly bool $auto = false,
        public readonly ?string $type = 'unscheduled', // only "unscheduled" supported currently
    ) {
        if ($this->interval < 1 || $this->interval > 12) {
            throw new TelrValidationException('repeat.interval must be between 1 and 12.');
        }

        if ($this->term !== null && ($this->term < 0 || $this->term > 48)) {
            throw new TelrValidationException('repeat.term must be between 0 (unlimited) and 48.');
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'customerid' => $this->customerId,
            // Converted to integer minor units per the documented XML schema for <repeat><amount>.
            'amount' => (string) AmountFormatter::toMinorUnits($this->amountMajorUnits),
            'period' => $this->period->value,
            'interval' => $this->interval,
            'start' => $this->start,
            'term' => $this->term,
            'final' => $this->final,
            'auto' => $this->auto ? '1' : null,
            'type' => $this->type,
        ]);
    }
}
