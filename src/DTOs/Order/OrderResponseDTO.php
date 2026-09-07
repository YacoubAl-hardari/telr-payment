<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\Enums\InvoiceStatus;
use yacoubalhaidari\Telr\Enums\OrderStatus;

/**
 * Unified response for order.json "create" and "check" calls, covering
 * order results, invoice results, and error blocks.
 */
final class OrderResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $ref = null,
        public readonly ?string $paymentUrl = null,
        public readonly ?string $cartId = null,
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $description = null,
        public readonly ?OrderStatus $orderStatus = null,
        public readonly ?InvoiceStatus $invoiceStatus = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
        public readonly array $raw = [],
    ) {
    }

    public static function fromApiResponse(array $response): self
    {
        if (isset($response['order'])) {
            $order = $response['order'];
            $statusCode = $order['status']['code'] ?? null;

            return new self(
                success: true,
                ref: $order['ref'] ?? null,
                paymentUrl: $order['url'] ?? null,
                cartId: $order['cartid'] ?? null,
                amount: $order['amount'] ?? null,
                currency: $order['currency'] ?? null,
                description: $order['description'] ?? null,
                orderStatus: $statusCode !== null ? OrderStatus::tryFrom((int) $statusCode) : null,
                raw: $response,
            );
        }

        if (isset($response['invoice'])) {
            $invoice = $response['invoice'];
            $statusCode = $invoice['status']['code'] ?? null;

            return new self(
                success: true,
                ref: $invoice['ref'] ?? null,
                amount: $invoice['amount'] ?? null,
                currency: $invoice['currency'] ?? null,
                description: $invoice['description'] ?? null,
                invoiceStatus: $statusCode !== null ? InvoiceStatus::tryFrom((int) $statusCode) : null,
                raw: $response,
            );
        }

        return new self(
            success: false,
            errorCode: $response['error']['message'] ?? null,
            errorMessage: $response['error']['note'] ?? null,
            raw: $response,
        );
    }
}
