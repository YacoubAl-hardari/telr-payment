<?php

namespace yacoubalhaidari\Telr\Enums;

enum TransactionClass: string
{
    case ECOMMERCE            = 'ecom';
    case MAIL_TELEPHONE_ORDER = 'moto';
    case CONTINUOUS_AUTHORITY = 'cont';
    case POINT_OF_SALE        = 'pos';

    /**
     * Numeric codes used by the Service API <class><code> element.
     */
    public function numericCode(): int
    {
        return match ($this) {
            self::MAIL_TELEPHONE_ORDER => 1,
            self::ECOMMERCE            => 2,
            self::CONTINUOUS_AUTHORITY => 4,
            self::POINT_OF_SALE        => 0, // not documented with a numeric code
        };
    }
}
