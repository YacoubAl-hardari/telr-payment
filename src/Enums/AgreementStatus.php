<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Repeat billing agreement status (Service API <agreement><status> numeric code).
 */
enum AgreementStatus: int
{
    case PENDING = 0;
    case RUNNING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case OVERDUE = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }
}
