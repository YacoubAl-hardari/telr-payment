<?php

namespace yacoubalhaidari\Telr;

use yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface;
use yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO;
use yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO;
use yacoubalhaidari\Telr\Services\AgreementService;
use yacoubalhaidari\Telr\Services\InvoiceService;
use yacoubalhaidari\Telr\Services\QuickLinkService;
use yacoubalhaidari\Telr\Services\ServiceApiService;
use yacoubalhaidari\Telr\Services\WebhookService;

/**
 * Central facade root. Exposes every Telr sub-service through one object,
 * plus proxies the most common PaymentGatewayInterface methods directly
 * for convenience: Telr::createOrder(...) instead of Telr::payments()->createOrder(...).
 */
class Telr
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected QuickLinkService $quickLink,
        protected InvoiceService $invoice,
        protected AgreementService $agreement,
        protected WebhookService $webhook,
        protected ?ServiceApiService $serviceApi = null,
    ) {
    }

    public function payments(): PaymentGatewayInterface
    {
        return $this->paymentGateway;
    }

    public function quickLinks(): QuickLinkService
    {
        return $this->quickLink;
    }

    public function invoices(): InvoiceService
    {
        return $this->invoice;
    }

    public function agreements(): AgreementService
    {
        return $this->agreement;
    }

    public function webhooks(): WebhookService
    {
        return $this->webhook;
    }

    public function serviceApi(): ServiceApiService
    {
        if (!$this->serviceApi) {
            throw new \RuntimeException(
                'Telr Service API credentials are not configured. Set TELR_SERVICE_API_MERCHANT_ID and TELR_SERVICE_API_KEY.'
            );
        }

        return $this->serviceApi;
    }

    public function createOrder(CreateOrderDTO $order): OrderResponseDTO
    {
        return $this->paymentGateway->createOrder($order);
    }

    public function checkOrderStatus(string $orderRef): OrderResponseDTO
    {
        return $this->paymentGateway->checkOrderStatus($orderRef);
    }

    public function checkInvoiceStatus(string $invoiceRef): OrderResponseDTO
    {
        return $this->paymentGateway->checkInvoiceStatus($invoiceRef);
    }
}
