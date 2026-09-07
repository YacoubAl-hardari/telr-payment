<?php

namespace yacoubalhaidari\Telr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface payments()
 * @method static \yacoubalhaidari\Telr\Services\QuickLinkService quickLinks()
 * @method static \yacoubalhaidari\Telr\Services\InvoiceService invoices()
 * @method static \yacoubalhaidari\Telr\Services\AgreementService agreements()
 * @method static \yacoubalhaidari\Telr\Services\WebhookService webhooks()
 * @method static \yacoubalhaidari\Telr\Services\ServiceApiService serviceApi()
 * @method static \yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO createOrder(\yacoubalhaidari\Telr\DTOs\Order\CreateOrderDTO $order)
 * @method static \yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO checkOrderStatus(string $orderRef)
 * @method static \yacoubalhaidari\Telr\DTOs\Order\OrderResponseDTO checkInvoiceStatus(string $invoiceRef)
 *
 * @see \yacoubalhaidari\Telr\Telr
 */
class Telr extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telr';
    }
}
