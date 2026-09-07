<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

/**
 * Optional customer block for order.json. Supplying this pre-fills the
 * Hosted Payment Page; incomplete or invalid fields simply cause the
 * payment page to prompt for the missing/incorrect part.
 *
 * `ref` (bill_custref) is required to enable the Stored Cards flow.
 */
final class CustomerDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?CustomerNameDTO $name = null,
        public readonly ?CustomerAddressDTO $address = null,
        public readonly ?string $phone = null,
        public readonly ?string $ref = null,
    ) {
    }

    public function toArray(): array
    {
        return self::filter([
            'ref' => $this->ref,
            'email' => $this->email,
            'name' => $this->name?->toArray(),
            'address' => $this->address?->toArray(),
            'phone' => $this->phone,
        ]);
    }
}
