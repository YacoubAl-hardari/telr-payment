<?php

namespace yacoubalhaidari\Telr\Contracts;

use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO;

interface PaymentGatewayInterface
{
    public function createOrder(CreateOrderDTO $order): OrderResponseDTO;

    public function checkOrderStatus(string $orderRef): OrderResponseDTO;

    public function checkInvoiceStatus(string $invoiceRef): OrderResponseDTO;
}
