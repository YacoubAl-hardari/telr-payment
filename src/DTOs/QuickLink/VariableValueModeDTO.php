<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

/**
 * "Donation" mode. Mutually exclusive with RepeatBilling and StockControl
 * per the QuickLink Configuration documentation.
 */
final class VariableValueModeDTO extends BaseDTO
{
    public function __construct(
        public readonly bool $status = true,
        public readonly ?string $sectionTitle = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'Status' => $this->status ? 1 : 0,
            'SectionTitle' => $this->sectionTitle,
        ]);
    }
}
