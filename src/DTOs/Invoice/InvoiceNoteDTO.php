<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class InvoiceNoteDTO extends BaseDTO
{
    public function __construct(public readonly string $text)
    {
    }

    public function toArray(): array
    {
        return ['text' => $this->text];
    }
}
