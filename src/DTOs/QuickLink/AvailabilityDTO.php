<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class AvailabilityDTO extends BaseDTO
{
    public function __construct(
        public readonly ?AvailabilityDateDTO $notValidBefore = null,
        public readonly ?AvailabilityDateDTO $notValidAfter = null,
        public readonly bool $stockControl = false,
        public readonly ?int $stockCount = null,
        public readonly bool $showStock = false,
        public readonly ?string $stockTitle = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'NotValidBefore' => $this->notValidBefore?->toArray(),
            'NotValidAfter' => $this->notValidAfter?->toArray(),
            'StockControl' => $this->stockControl ? 1 : null,
            'StockCount' => $this->stockCount,
            'ShowStock' => $this->showStock ? 1 : null,
            'StockTitle' => $this->stockTitle,
        ]);
    }
}
