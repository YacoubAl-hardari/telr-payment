<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class WebhookUrlDTO extends BaseDTO
{
    public function __construct(public readonly string $url)
    {
    }

    public function toArray(): array
    {
        return ['url' => $this->url];
    }
}
