<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Transaction types. The `value` matches the string used in webhook tran_type;
 * the numeric Service API code is available via numericCode().
 */
enum TransactionType: string
{
    case SALE             = 'sale';
    case VOID             = 'void';
    case REFUND           = 'refund';
    case REFUND_REVERSAL  = 'refund_reversal';
    case AUTH             = 'auth';
    case RELEASE          = 'release';
    case CAPTURE          = 'capture';
    case CAPTURE_REVERSAL = 'capture_reversal';

    /**
     * Numeric codes as used by the Service API <type><code> element.
     */
    public function numericCode(): int
    {
        return match ($this) {
            self::SALE             => 1,
            self::VOID             => 2,
            self::REFUND           => 3,
            self::REFUND_REVERSAL  => 4,
            self::AUTH             => 5,
            self::RELEASE          => 6,
            self::CAPTURE          => 7,
            self::CAPTURE_REVERSAL => 8,
        };
    }

    public static function fromNumericCode(int $code): self
    {
        return match ($code) {
            1 => self::SALE,
            2 => self::VOID,
            3 => self::REFUND,
            4 => self::REFUND_REVERSAL,
            5 => self::AUTH,
            6 => self::RELEASE,
            7 => self::CAPTURE,
            8 => self::CAPTURE_REVERSAL,
            default => throw new \ValueError("Unknown transaction type code: {$code}"),
        };
    }
}
