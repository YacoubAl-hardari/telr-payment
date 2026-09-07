<?php

namespace yacoubalhaidari\Telr\DTOs\Invoice;

final class InvoiceResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $reference = null,
        public readonly ?string $paymentUrl = null,
        public readonly ?string $errorMessage = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param  array<string,mixed>  $parsed  Result of parsing the XML response
     *                                        (e.g. via simplexml + json_decode(json_encode())).
     */
    public static function fromParsedXml(array $parsed): self
    {
        $status = $parsed['status'] ?? null;

        if ($status === 'OK') {
            return new self(
                success: true,
                reference: $parsed['reference'] ?? null,
                paymentUrl: $parsed['url'] ?? null,
                raw: $parsed,
            );
        }

        return new self(
            success: false,
            errorMessage: $parsed['message'] ?? 'Unknown error creating invoice.',
            raw: $parsed,
        );
    }
}
