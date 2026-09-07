<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

final class QuickLinkDetailsDTO extends BaseDTO
{
    public function __construct(
        public readonly string $desc,
        public readonly string $cart,
        public readonly Currency $currency,
        public readonly string $amount,
        public readonly ?int $minQuantity = null,
        public readonly ?int $maxQuantity = null,
        public readonly ?string $fullName = null,
        public readonly ?string $addr1 = null,
        public readonly ?string $city = null,
        public readonly ?string $country = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?int $agreementId = null,
        public readonly bool $changeCard = false,
    ) {
        if (mb_strlen($desc) < 2 || mb_strlen($desc) > 60) {
            throw new TelrValidationException('QuickLink Details.Desc must be between 2 and 60 characters (it will be truncated by the API beyond 60).');
        }

        if (mb_strlen($cart) > 63) {
            throw new TelrValidationException('QuickLink Details.Cart must not exceed 63 characters.');
        }

        if ($this->changeCard && !$this->agreementId) {
            throw new TelrValidationException('Details.AgreementID is required when Details.ChangeCard is true.');
        }
    }

    public function toArray(): array
    {
        return self::filter([
            'Desc' => $this->desc,
            'Cart' => $this->cart,
            'Currency' => $this->currency->value,
            'Amount' => $this->amount,
            'MinQuantity' => $this->minQuantity,
            'MaxQuantity' => $this->maxQuantity,
            'FullName' => $this->fullName,
            'Addr1' => $this->addr1,
            'City' => $this->city,
            'Country' => $this->country,
            'Email' => $this->email,
            'Phone' => $this->phone,
            'AgreementID' => $this->agreementId,
            'ChangeCard' => $this->changeCard ? true : null,
        ]);
    }
}
