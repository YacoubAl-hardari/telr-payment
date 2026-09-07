<?php

namespace yacoubalhaidari\Telr\Services;

use yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface;
use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO;

/**
 * Wraps POST /gateway/order.json for both "create" (Hosted Payment Page
 * session) and "check" (order/invoice status) methods.
 */
class PaymentPageService implements PaymentGatewayInterface
{
    protected const ENDPOINT = '/gateway/order.json';

    public function __construct(
        protected HttpClient $client,
        protected string $store,
        protected string $authKey,
    ) {
    }

    public function createOrder(CreateOrderDTO $order): OrderResponseDTO
    {
        $payload = $order->toArray($this->store, $this->authKey);
        $response = $this->client->postJson(self::ENDPOINT, $payload);

        return OrderResponseDTO::fromApiResponse($response);
    }

    public function checkOrderStatus(string $orderRef): OrderResponseDTO
    {
        $response = $this->client->postJson(self::ENDPOINT, [
            'method' => 'check',
            'store' => $this->store,
            'authkey' => $this->authKey,
            'order' => ['ref' => $orderRef],
        ]);

        return OrderResponseDTO::fromApiResponse($response);
    }

    public function checkInvoiceStatus(string $invoiceRef): OrderResponseDTO
    {
        $response = $this->client->postJson(self::ENDPOINT, [
            'method' => 'check',
            'store' => $this->store,
            'authkey' => $this->authKey,
            'invoiceref' => $invoiceRef,
        ]);

        return OrderResponseDTO::fromApiResponse($response);
    }
}
