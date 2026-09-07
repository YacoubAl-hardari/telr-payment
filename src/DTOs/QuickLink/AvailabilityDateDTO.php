<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class AvailabilityDateDTO extends BaseDTO
{
    public function __construct(
        public readonly int $day,
        public readonly int $month,
        public readonly int $year,
        public readonly ?int $hour = null,
        public readonly ?int $minute = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'Day' => $this->day,
            'Month' => $this->month,
            'Year' => $this->year,
            'Hour' => $this->hour,
            'Minute' => $this->minute,
        ]);
    }
}
