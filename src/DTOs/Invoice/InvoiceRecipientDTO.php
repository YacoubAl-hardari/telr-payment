<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;

final class InvoiceRecipientDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $title = null,
        public readonly ?string $first = null,
        public readonly ?string $last = null,
        public readonly ?string $line1 = null,
        public readonly ?string $line2 = null,
        public readonly ?string $line3 = null,
        public readonly ?string $city = null,
        public readonly ?string $region = null,
        public readonly ?string $country = null, // 2-letter ISO
        public readonly ?string $zip = null,
    ) {
    }

    public function toArray(): array
    {
        $name = self::filter([
            'title' => $this->title,
            'first' => $this->first,
            'last' => $this->last,
        ]);

        $address = self::filter([
            'line1' => $this->line1,
            'line2' => $this->line2,
            'line3' => $this->line3,
            'city' => $this->city,
            'region' => $this->region,
            'country' => $this->country,
            'zip' => $this->zip,
        ]);

        return self::filter([
            'name' => $name,
            'address' => $address,
            'email' => $this->email,
        ]);
    }
}
