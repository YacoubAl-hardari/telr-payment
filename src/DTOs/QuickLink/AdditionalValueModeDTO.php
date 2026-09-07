<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Optional tip/additional-value percentage. Mutually exclusive with
 * RepeatBilling, StockControl and Split Payment.
 */
final class AdditionalValueModeDTO extends BaseDTO
{
    public function __construct(
        public readonly bool $status = true,
        public readonly ?string $sectionTitle = null,
        public readonly ?int $percentage = null,
    ) {
        if ($sectionTitle !== null && mb_strlen($sectionTitle) > 64) {
            throw new TelrValidationException('AdditionalValueMode.SectionTitle cannot exceed 64 characters.');
        }

        if ($percentage !== null && ($percentage < 0 || $percentage > 50)) {
            throw new TelrValidationException('AdditionalValueMode.Percentage must be between 0 and 50.');
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'Status' => $this->status ? 1 : 0,
            'SectionTitle' => $this->sectionTitle,
            'Percentage' => $this->percentage,
        ]);
    }
}
