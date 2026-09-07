<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

final class SplitDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id, // configured split-payout ID, or 0 for ad-hoc
        public readonly ?string $type = null, // 'flat' | 'percentage' | 'remaining'
        public readonly ?string $value = null,
    ) {
        if ($this->type === 'remaining' && $this->value !== null) {
            throw new TelrValidationException('Split type "remaining" must not have a value.');
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'id' => $this->id,
            'type' => $this->type,
            'value' => $this->value,
        ]);
    }
}
