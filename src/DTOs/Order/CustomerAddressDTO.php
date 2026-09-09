<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class CustomerAddressDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $line1 = null,
        public readonly ?string $city = null,
        public readonly ?string $country = null, // 2-letter ISO code
        public readonly ?string $line2 = null,
        public readonly ?string $line3 = null,
        public readonly ?string $state = null,
        public readonly ?string $areacode = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'line1' => $this->line1,
            'line2' => $this->line2,
            'line3' => $this->line3,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'areacode' => $this->areacode,
        ]);
    }
}
