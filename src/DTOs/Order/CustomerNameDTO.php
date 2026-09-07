<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class CustomerNameDTO extends BaseDTO
{
    public function __construct(
        public readonly string $forenames,
        public readonly string $surname,
        public readonly ?string $title = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'title' => $this->title,
            'forenames' => $this->forenames,
            'surname' => $this->surname,
        ]);
    }
}
