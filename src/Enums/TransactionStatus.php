<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Transaction status as returned in tran_status (webhook) and auth.status (Service API).
 */
enum TransactionStatus: string
{
    case AUTHORISED = 'A';
    case HOLD       = 'H';
    case CANCELLED  = 'C';
    case DECLINED   = 'D';
    case ERROR      = 'E';
    case EXPIRED    = 'X';

    /**
     * 'A' or 'H' indicate an authorised transaction. Any other value indicates
     * the request could not be processed.
     */
    public function isAuthorised(): bool
    {
        return in_array($this, [self::AUTHORISED, self::HOLD], true);
    }

    public function isOnHold(): bool
    {
        return $this === self::HOLD;
    }
}
