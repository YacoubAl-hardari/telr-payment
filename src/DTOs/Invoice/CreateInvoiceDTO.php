<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\Currency;
use yacoubalhaidari\Telr\Enums\InvoiceLayout;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Root DTO mapping to the <invoice> XML body for
 * POST https://secure.telr.com/gateway/invoice_create.xml
 *
 * Set $details to null to link an existing external invoicing system's
 * email (per "Linking to existing email invoices") — the API will still
 * return a reference and payment URL but will not send an email itself.
 *
 * @param  array<string,string>  $extra  Maximum 7 key/value pairs.
 */
final class CreateInvoiceDTO extends BaseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly Currency $currency,
        public readonly string $amount,
        public readonly InvoiceLayout $layout,
        public readonly InvoiceRecipientDTO $recipient,
        public readonly ?InvoiceDetailsSectionDTO $details = null,
        public readonly bool $test = false,
        public readonly ?InvoiceRepeatDTO $repeat = null,
        public readonly ?InvoiceEarlyPaymentDTO $early = null,
        public readonly ?InvoiceLatePaymentDTO $late = null,
        public readonly array $extra = [],
    ) {
        if (count($this->extra) > 7) {
            throw new TelrValidationException('A maximum of 7 extra fields are allowed per invoice.');
        }

        if ($this->layout->value > 3 && $this->repeat) {
            // repeat billing item/total layouts documented (1-3); still allow but flag common mistake
        }
    }

    /**
     * Build the array structure that XmlArraySerializer will turn into
     * the final <invoice>...</invoice> body. $store and $password are
     * injected by InvoiceService at send time (kept out of the DTO so it
     * stays free of credentials).
     */
    public function toArray(string $store, string $password): array
    {
        $data = self::filter([
            'store' => $store,
            'password' => $password,
            'title' => $this->title,
            'testmode' => $this->test ? '1' : '0',
            'currency' => $this->currency->value,
            'amount' => $this->amount,
            'layout' => $this->layout->value,
            'repeat' => $this->repeat?->toArray(),
            'early' => $this->early?->toArray(),
            'late' => $this->late?->toArray(),
            'recipient' => $this->recipient->toArray(),
            'extra' => $this->buildExtra(),
        ]);

        if ($this->details) {
            $data['details'] = $this->details->toArray();
        }

        return $data;
    }

    protected function buildExtra(): array
    {
        if ($this->extra === []) {
            return [];
        }

        $extra = [];
        $i = 1;
        foreach ($this->extra as $value) {
            $extra["extra{$i}"] = $value;
            $i++;
        }

        return $extra;
    }
}
