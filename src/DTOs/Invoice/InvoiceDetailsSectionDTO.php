<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * @param  InvoiceItemDTO[]  $items  At least one item is required.
 * @param  InvoiceTotalDTO[]  $totals  If omitted, a single "Total" line using <invoice><amount> is used.
 * @param  InvoiceNoteDTO[]  $notes  Optional promotional/informational lines.
 */
final class InvoiceDetailsSectionDTO extends BaseDTO
{
    public function __construct(
        public readonly array $items,
        public readonly array $totals = [],
        public readonly array $notes = [],
    ) {
        if ($this->items === []) {
            throw new TelrValidationException('At least one invoice item is required in the details section.');
        }
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->items !== []) {
            $data['item'] = array_map(fn (InvoiceItemDTO $i) => $i->toArray(), $this->items);
        }

        if ($this->totals !== []) {
            $data['total'] = array_map(fn (InvoiceTotalDTO $t) => $t->toArray(), $this->totals);
        }

        if ($this->notes !== []) {
            $data['note'] = array_map(fn (InvoiceNoteDTO $n) => $n->toArray(), $this->notes);
        }

        return $data;
    }
}
