<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class ReturnUrlsDTO extends BaseDTO
{
    public function __construct(
        public readonly string $authorised,
        public readonly string $declined,
        public readonly string $cancelled,
    ) {
    }

    public function toArray(): array
    {
        return [
            'authorised' => $this->authorised,
            'declined' => $this->declined,
            'cancelled' => $this->cancelled,
        ];
    }
}
