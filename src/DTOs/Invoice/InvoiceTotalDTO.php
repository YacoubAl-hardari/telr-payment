<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class InvoiceTotalDTO extends BaseDTO
{
    public function __construct(
        public readonly string $text,
        public readonly string $amount,
    ) {
    }

    public function toArray(): array
    {
        return ['text' => $this->text, 'amount' => $this->amount];
    }
}
