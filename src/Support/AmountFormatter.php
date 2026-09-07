<?php

namespace yacoubalhaidari\Telr\Support;

/**
 * Helpers for the amount formatting rules documented across order.json,
 * api_quicklink.json and invoice_create.xml:
 *   - major units only (e.g. 9.50, not 950)
 *   - no thousands separators
 *   - decimal point for the minor unit
 */
class AmountFormatter
{
    /**
     * Format a numeric value (float, int, or numeric string) as a Telr-safe
     * major-unit amount string, e.g. 9.5 => "9.50".
     */
    public static function toMajorUnits(float|int|string $amount, int $decimals = 2): string
    {
        return number_format((float) $amount, $decimals, '.', '');
    }

    /**
     * Convert an integer amount in minor units (cents/fils) to a major-unit
     * string, e.g. 950 => "9.50".
     */
    public static function fromMinorUnits(int $minorUnits, int $decimals = 2): string
    {
        return number_format($minorUnits / (10 ** $decimals), $decimals, '.', '');
    }

    /**
     * Convert a major-unit amount string/float to integer minor units,
     * e.g. "9.50" => 950. Used by the <repeat><amount> field in the
     * remote XML invoice API, which is documented as being sent in
     * integer minor units (unlike every other amount field).
     */
    public static function toMinorUnits(float|string $amount, int $decimals = 2): int
    {
        return (int) round(((float) $amount) * (10 ** $decimals));
    }

    public static function isValid(string $amount): bool
    {
        return (bool) preg_match('/^\d+(\.\d{1,2})?$/', $amount) && !str_contains($amount, ',');
    }
}
