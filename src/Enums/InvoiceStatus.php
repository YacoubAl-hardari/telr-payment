<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Invoice status codes as returned by the order.json "check" method
 * when checking an invoiceref.
 */
enum InvoiceStatus: int
{
    case PENDING = 1;
    case NOT_PAID = 2;
    case PAID = 4;
    case CANCELLED = 6;
    case EXPIRED = 7;
}
