<?php

namespace yacoubalhaidari\Telr\Services;

use yacoubalhaidari\Telr\Contracts\InvoiceInterface;
use yacoubalhaidari\Telr\DTOs\Invoice\CreateInvoiceDTO;
use yacoubalhaidari\Telr\DTOs\Invoice\InvoiceResponseDTO;
use yacoubalhaidari\Telr\Exceptions\TelrApiException;
use yacoubalhaidari\Telr\Support\XmlArraySerializer;

/**
 * Wraps POST https://secure.telr.com/gateway/invoice_create.xml
 *
 * Unlike every other Telr endpoint in this package, the remote invoicing
 * interface is XML-based and authenticates using a dedicated invoice
 * password (configured under the Remote section of the invoice
 * configuration page in Merchant Admin) rather than the HPP authkey.
 */
class InvoiceService implements InvoiceInterface
{
    protected const ENDPOINT = '/gateway/invoice_create.xml';

    public function __construct(
        protected HttpClient $client,
        protected string $store,
        protected string $invoicePassword,
    ) {
    }

    public function create(CreateInvoiceDTO $invoice): InvoiceResponseDTO
    {
        $data = $invoice->toArray($this->store, $this->invoicePassword);
        $xmlBody = XmlArraySerializer::toXml('invoice', $data);

        $responseXml = $this->client->postXml(self::ENDPOINT, $xmlBody);
        $parsed = $this->parseResponse($responseXml);

        return InvoiceResponseDTO::fromParsedXml($parsed);
    }

    protected function parseResponse(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);
        $element = simplexml_load_string($xml);
        libxml_use_internal_errors($previous);

        if ($element === false) {
            throw new TelrApiException('Unable to parse XML response from invoice_create.xml.');
        }

        return json_decode((string) json_encode($element), true) ?: [];
    }
}
