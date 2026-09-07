<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

/**
 * A single line item. Which fields are used depends on the invoice
 * layout: Layout 1 uses text/cost only; Layout 2 adds quantity/amount;
 * Layout 3 additionally supports a discount.
 */
final class InvoiceItemDTO extends BaseDTO
{
    public function __construct(
        public readonly string $text,
        public readonly string $cost,
        public readonly ?int $quantity = null,
        public readonly ?string $discount = null,
        public readonly ?string $amount = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'text' => $this->text,
            'cost' => $this->cost,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'amount' => $this->amount,
        ]);
    }
}
