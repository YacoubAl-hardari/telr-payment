<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;
use yacoubalhaidari\Telr\Support\AmountFormatter;

final class OrderDetailsDTO extends BaseDTO
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $amount,
        public readonly Currency $currency,
        public readonly string $description,
        public readonly bool $test = false,
        public readonly ?string $trantype = null,
    ) {
        if ($cartId === '' || mb_strlen($cartId) > 63) {
            throw new TelrValidationException('order.cartid is required and must not exceed 63 characters.');
        }

        if ($description === '' || mb_strlen($description) > 63) {
            throw new TelrValidationException('order.description is required and must not exceed 63 characters.');
        }

        if (!AmountFormatter::isValid($amount)) {
            throw new TelrValidationException("order.amount [{$amount}] must be in major units, e.g. 9.50, with no thousands separators.");
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'cartid' => $this->cartId,
            'test' => $this->test ? '1' : '0',
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'description' => $this->description,
            'trantype' => $this->trantype,
        ]);
    }
}
