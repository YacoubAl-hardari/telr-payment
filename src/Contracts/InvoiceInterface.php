<?php

namespace yacoubalhaidari\Telr\Contracts;

use yacoubalhaidari\Telr\DTOs\Invoice\CreateInvoiceDTO;
use yacoubalhaidari\Telr\DTOs\Invoice\InvoiceResponseDTO;

interface InvoiceInterface
{
    public function create(CreateInvoiceDTO $invoice): InvoiceResponseDTO;
}
