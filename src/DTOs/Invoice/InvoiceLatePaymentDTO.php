<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

/**
 * Penalty for paying after a given date. Invoices viewed after this date
 * are also marked as overdue. All four fields are required together.
 */
final class InvoiceLatePaymentDTO extends BaseDTO
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
