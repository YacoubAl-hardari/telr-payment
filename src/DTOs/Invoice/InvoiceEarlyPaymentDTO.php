<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

/**
 * Discount for paying before a given date. All four fields are required
 * together, per the "Remote creation of invoices" documentation.
 */
final class InvoiceEarlyPaymentDTO extends BaseDTO
{
    public function __construct(
        public readonly int $day,
        public readonly int $month,
        public readonly int $year,
        public readonly string $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'amount' => $this->amount,
        ];
    }
}
