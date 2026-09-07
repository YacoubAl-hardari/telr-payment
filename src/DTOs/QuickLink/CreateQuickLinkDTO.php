<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Maps to the QuickLinkRequest body documented on the "Create QuickLink"
 * page (POST /gateway/api_quicklink.json).
 *
 * @param  SplitDTO[]  $splits  Percentages across all entries must sum to <= 100%.
 * @param  array<string,string>  $extra  Maximum 7 key/value pairs.
 */
final class CreateQuickLinkDTO extends BaseDTO
{
    public function __construct(
        public readonly QuickLinkDetailsDTO $details,
        public readonly ?VariableValueModeDTO $variableValueMode = null,
        public readonly ?AdditionalValueModeDTO $additionalValueMode = null,
        public readonly ?RepeatBillingDTO $repeatBilling = null,
        public readonly ?AvailabilityDTO $availability = null,
        public readonly array $splits = [],
        public readonly array $extra = [],
    ) {
        if (count($this->extra) > 7) {
            throw new TelrValidationException('A maximum of 7 extra fields are allowed per QuickLink.');
        }

        if ($this->repeatBilling && $this->details->agreementId) {
            throw new TelrValidationException('RepeatBilling is not allowed when Details.AgreementID is set.');
        }

        $exclusiveModesActive = array_filter([
            (bool) $this->variableValueMode,
            (bool) $this->repeatBilling,
        ]);

        if (count($exclusiveModesActive) > 1) {
            throw new TelrValidationException('VariableValueMode and RepeatBilling cannot both be enabled on the same QuickLink.');
        }
    }

    public function toArray(string $storeId, string $authKey): array
    {
        return [
            'QuickLinkRequest' => self::filter([
                'StoreID' => (int) $storeId,
                'AuthKey' => $authKey,
                'Details' => $this->details->toArray(),
                'VariableValueMode' => $this->variableValueMode?->toArray(),
                'AdditionalValueMode' => $this->additionalValueMode?->toArray(),
                'RepeatBilling' => $this->repeatBilling?->toArray(),
                'Availability' => $this->availability?->toArray(),
                'Splits' => array_map(fn (SplitDTO $s) => $s->toArray(), $this->splits),
                'extra' => $this->extra,
            ]),
        ];
    }
}
