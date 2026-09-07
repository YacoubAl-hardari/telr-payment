# Telr Laravel
<img width="100%" height="100%" alt="telr laravel paymwnt" src="https://github.com/user-attachments/assets/5f57dbcd-f676-456e-8dd5-bbdce6e73c27" />


Laravel package for the [Telr](https://telr.com) Payment Gateway: Hosted Payment Page, QuickLinks, Remote Invoicing, Repeat Billing Agreements, Webhooks, and the Service API — built directly against Telr's published API documentation.

## Installation

```bash
composer require yacoubalhaidari/telr-laravel
```

Laravel's package auto-discovery will register `TelrServiceProvider` and the `Telr` facade automatically.

Publish the config file:

```bash
php artisan vendor:publish --tag=telr-config
```

Optionally publish the example `telr_transactions` migration:

```bash
php artisan vendor:publish --tag=telr-migrations
php artisan migrate
```

## Configuration

Add to your `.env`:

```env
TELR_STORE_ID=1234
TELR_AUTH_KEY=mykey1234
TELR_SECRET_KEY=your-webhook-secret-key
TELR_INVOICE_PASSWORD=your-remote-invoice-password
TELR_BASE_URL=https://secure.telr.com
TELR_TEST_MODE=true

TELR_RETURN_AUTHORISED=https://yourapp.com/checkout/authorised
TELR_RETURN_DECLINED=https://yourapp.com/checkout/declined
TELR_RETURN_CANCELLED=https://yourapp.com/checkout/cancelled

TELR_WEBHOOK_PATH=webhooks/telr

TELR_SERVICE_API_MERCHANT_ID=
TELR_SERVICE_API_KEY=
```

Configure the webhook URL in Merchant Admin under **Payment Page / Security → Transaction advice** to point at:

```
https://yourapp.com/webhooks/telr
```

(or whatever `TELR_WEBHOOK_PATH` you set).

> The webhook route is registered without the `web` middleware group since it's a direct server-to-server POST. If your app's `VerifyCsrfToken` middleware is applied globally elsewhere, add the path to its `$except` array as well.

## Usage

### 1. Hosted Payment Page (create + check an order)

```php
use yacoubalhaidari\Telr\Facades\Telr;
use yacoubalhaidari\Telr\DTOs\Order\{CreateOrderDTO, OrderDetailsDTO, ReturnUrlsDTO};
use yacoubalhaidari\Telr\Enums\Currency;

$response = Telr::createOrder(new CreateOrderDTO(
    order: new OrderDetailsDTO(
        cartId: (string) $order->id,
        amount: '150.00',
        currency: Currency::AED,
        description: 'Order #' . $order->id,
        test: config('telr.test_mode'),
    ),
    return: new ReturnUrlsDTO(
        authorised: route('checkout.authorised'),
        declined: route('checkout.declined'),
        cancelled: route('checkout.cancelled'),
    ),
));

if ($response->success) {
    return redirect($response->paymentUrl);
}

// $response->errorCode / $response->errorMessage
```

Check status later:

```php
$status = Telr::checkOrderStatus($orderRef);
$status->orderStatus; // yacoubalhaidari\Telr\Enums\OrderStatus
```

### 2. QuickLinks

```php
use yacoubalhaidari\Telr\Facades\Telr;
use yacoubalhaidari\Telr\DTOs\QuickLink\{CreateQuickLinkDTO, QuickLinkDetailsDTO};
use yacoubalhaidari\Telr\Enums\Currency;

$response = Telr::quickLinks()->create(new CreateQuickLinkDTO(
    details: new QuickLinkDetailsDTO(
        desc: 'Concert ticket',
        cart: 'evt-2026-001',
        currency: Currency::AED,
        amount: '250.00',
    ),
));

$response->url; // shareable payment link
```

Update the card on an existing repeat-billing agreement:

```php
Telr::quickLinks()->requestCardChange(
    agreementId: '597071',
    email: 'customer@example.com',
    currency: Currency::AED,
);
```

### 3. Remote Invoicing (XML)

```php
use yacoubalhaidari\Telr\Facades\Telr;
use yacoubalhaidari\Telr\DTOs\Invoice\{
    CreateInvoiceDTO, InvoiceDetailsSectionDTO, InvoiceItemDTO,
    InvoiceTotalDTO, InvoiceRecipientDTO
};
use yacoubalhaidari\Telr\Enums\{Currency, InvoiceLayout};

$response = Telr::invoices()->create(new CreateInvoiceDTO(
    title: 'Invoice for services rendered',
    currency: Currency::AED,
    amount: '134.95',
    layout: InvoiceLayout::BASIC,
    recipient: new InvoiceRecipientDTO(email: 'client@example.com', first: 'Jane', last: 'Doe'),
    details: new InvoiceDetailsSectionDTO(
        items: [
            new InvoiceItemDTO(text: 'Item 1', cost: '100.00'),
            new InvoiceItemDTO(text: 'Item 2', cost: '25.00'),
        ],
        totals: [
            new InvoiceTotalDTO(text: 'Sub Total', amount: '125.00'),
            new InvoiceTotalDTO(text: 'Postage', amount: '9.95'),
            new InvoiceTotalDTO(text: 'Total', amount: '134.95'),
        ],
    ),
));

$response->paymentUrl;
```

### 4. Repeat Billing Agreements

```php
use yacoubalhaidari\Telr\Facades\Telr;

Telr::agreements()->freeze('60100', '2026-10-01', '2026-11-01');
Telr::agreements()->changeDueDate(['60100'], '2026-12-25');

$forecast = Telr::agreements()->forecast('2026-09-07', '2026-09-30'); // ForecastAgreementItemDTO[]
$failed = Telr::agreements()->failed('2026-08-01', '2026-08-31');
$cancelled = Telr::agreements()->cancelled('2026-07-01', '2026-08-31');
```

### 5. Webhooks

Listen for the typed events dispatched by the built-in webhook controller:

```php
// EventServiceProvider
protected $listen = [
    \yacoubalhaidari\Telr\Events\TelrTransactionAuthorised::class => [
        \App\Listeners\MarkOrderAsPaid::class,
    ],
    \yacoubalhaidari\Telr\Events\TelrTransactionDeclined::class => [
        \App\Listeners\MarkOrderAsFailed::class,
    ],
    \yacoubalhaidari\Telr\Events\TelrAgreementCancelled::class => [
        \App\Listeners\HandleSubscriptionCancellation::class,
    ],
    \yacoubalhaidari\Telr\Events\TelrPayoutReceived::class => [
        \App\Listeners\RecordPayout::class,
    ],
];
```

```php
class MarkOrderAsPaid
{
    public function handle(\yacoubalhaidari\Telr\Events\TelrTransactionAuthorised $event): void
    {
        $tx = $event->transaction; // TransactionWebhookDTO
        Order::where('cart_id', $tx->cartId)->update(['status' => 'paid']);
    }
}
```

### 6. Service API (reporting / reconciliation)

```php
use yacoubalhaidari\Telr\Facades\Telr;

$xml = Telr::serviceApi()->transaction('011287290362');
$accounts = Telr::serviceApi()->accounts(); // JSON, unlike every other Service API endpoint
```

## Testing

```bash
composer install
composer test
```

## License

MIT
