<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Numeric event codes from the agreement history Service API endpoint.
 */
enum AgreementEventType: int
{
    case CREATED = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case INVOICE_LATE = 4;
    case CANCELLED = 5;
    case INVOICE_SENT = 6;
    case PAYMENT_AUTHORISED = 7;
    case PAYMENT_DECLINED = 8;
    case CARD_VERIFIED = 9;
    case CREDIT_AUTHORISED = 10;
    case CREDIT_DECLINED = 11;
    case TEXT_NOTE_ADDED = 12;
}
