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

    public static function fromAuthenticatedUser(): ?self
    {
        if (!function_exists('auth') || !($user = auth()->user())) {
            return null;
        }

        $fullName = trim((string) data_get($user, 'name', ''));
        $forenames = (string) data_get($user, 'first_name', '');
        $surname = (string) data_get($user, 'last_name', '');

        if ($forenames === '' && $surname === '' && $fullName !== '') {
            $nameParts = preg_split('/\s+/', $fullName, 2) ?: [];
            $forenames = $nameParts[0] ?? '';
            $surname = $nameParts[1] ?? '';
        }

        $name = $forenames !== '' || $surname !== ''
            ? new CustomerNameDTO($forenames, $surname)
            : null;

        $address = new CustomerAddressDTO(
            line1: data_get($user, 'address.line1', data_get($user, 'address_line1')),
            city: data_get($user, 'address.city', data_get($user, 'city')),
            country: data_get($user, 'country_code', data_get($user, 'country')),
        );

        return new self(
            email: data_get($user, 'email'),
            name: $name,
            address: $address->toArray() === [] ? null : $address,
            phone: data_get($user, 'phone'),
        );
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
