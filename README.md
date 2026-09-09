# Telr Laravel

<img width="100%" height="100%" alt="telr laravel paymwnt" src="https://github.com/user-attachments/assets/5f57dbcd-f676-456e-8dd5-bbdce6e73c27" />

Laravel package for the [Telr](https://telr.com) Payment Gateway: Hosted Payment Page, QuickLinks, Remote Invoicing, Repeat Billing Agreements, Webhooks, and the Service API — built directly against Telr's published API documentation.

## Installation

Supports Laravel 10, 11, 12, and 13. PHP 8.1 or newer is required by this
package; Laravel 13 requires PHP 8.3 or newer. Use the PHP version supported by
your selected Laravel release.

```bash
composer require yacoubalhaidari/telr-laravel
```

Laravel's package auto-discovery will register `TelrServiceProvider` and the `Telr` facade automatically.

Laravel 13 is supported by both Illuminate dependency constraints. No vendor
edits or application-side DTO patches are needed. Authenticated request DTOs
(`CreateOrderDTO`, `CreateQuickLinkDTO`, and `CreateInvoiceDTO`) extend
`AuthenticatedRequestDTO`; nested DTOs extend `BaseDTO`. Existing
`toArray(store, credential)` signatures, including named arguments, are preserved.

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
TELR_SHOW_INVOICE_DATA=false

TELR_RETURN_AUTHORISED=https://yourapp.com/checkout/authorised
TELR_RETURN_DECLINED=https://yourapp.com/checkout/declined
TELR_RETURN_CANCELLED=https://yourapp.com/checkout/cancelled

TELR_WEBHOOK_PATH=webhooks/telr
TELR_REGISTER_RETURN_ROUTE=false

TELR_SERVICE_API_MERCHANT_ID=
TELR_SERVICE_API_KEY=
```

Configure the webhook URL in Merchant Admin under **Payment Page / Security → Transaction advice** to point at:

```
https://yourapp.com/webhooks/telr
```

(or whatever `TELR_WEBHOOK_PATH` you set).

> The webhook route is registered without the `web` middleware group since it's a direct server-to-server POST. If your app's `VerifyCsrfToken` middleware is applied globally elsewhere, add the path to its `$except` array as well.

Transaction advice must use a nonempty `TELR_SECRET_KEY`. The verifier accepts
the optional `tran_order` signature field when enabled for your store and works
with form data already decoded by PHP. Do not URL-decode the request a second
time. See [Telr's signature specification](https://docs.telr.com/reference/webhook).

`TELR_SHOW_INVOICE_DATA` controls the **authenticated-user fallback** only.
When you pass `customer:` to `CreateOrderDTO`, that billing block is **always**
sent to the Hosted Payment Page so name and email can be pre-filled while the
customer still enters card details manually. Set the flag to `true` when you
want the package to build customer data from `auth()->user()` automatically:

```env
TELR_SHOW_INVOICE_DATA=true
```

Prefer an explicit customer from your domain model (order customer, merchant /
join request, subscription owner):

```php
use yacoubalhaidari\Telr\DTOs\Order\CustomerDTO;

$customer = CustomerDTO::fromContact(
    fullName: $order->customer_name,
    email: $order->customer_email,
    phone: $order->customer_phone,
);

$response = Telr::createOrder(new CreateOrderDTO(
    order: new OrderDetailsDTO(...),
    return: new ReturnUrlsDTO(...),
    customer: $customer,
));
```

Incomplete fields simply remain editable on Telr's page.

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

### 5. Browser return + Webhooks

**Browser return (recommended application pattern)**

Create the Telr order with one signed, payment-specific return URL for
authorised, declined, and cancelled outcomes. On return, reload the local
payment attempt and call `Telr::checkOrderStatus($savedOrderReference)`.
Only `OrderStatus::PAID` with a matching cart, amount, currency, and test mode
should deliver the service. YacoubAlHaidari.com's `TelrPaymentController` follows this
pattern.

Optionally enable the package's static return controller when you use
`TELR_RETURN_*` URLs instead of a payment-specific route:

```env
TELR_REGISTER_RETURN_ROUTE=true
TELR_RETURN_PATH=payments/telr/return/{ref?}
```

**Transaction advice webhooks**

Listen for the typed events dispatched by the built-in webhook controller:

```php
// EventServiceProvider
protected $listen = [
    \yacoubalhaidari\Telr\Events\TelrTransactionAuthorised::class => [
        \App\Listeners\ReconcileOrderPayment::class,
    ],
    \yacoubalhaidari\Telr\Events\TelrTransactionDeclined::class => [
        \App\Listeners\ReconcileOrderPayment::class,
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
class ReconcileOrderPayment
{
    public function __construct(private \App\Services\OrderPaymentReconciliation $payments) {}

    public function handle(
        \yacoubalhaidari\Telr\Events\TelrTransactionAuthorised|\yacoubalhaidari\Telr\Events\TelrTransactionDeclined $event,
    ): void
    {
        $this->payments->reconcile($event->transaction->cartId);
    }
}
```

`OrderPaymentReconciliation` above is an application service you implement.
It should resolve the stored checkout by cart ID and use
`Telr::checkOrderStatus($savedOrderReference)` to verify the reference, cart,
amount, currency, and environment before changing local state. Only
`OrderStatus::PAID` confirms capture; an authorised event can represent a hold.
Use a transaction and idempotent updates so repeated advice cannot deliver an
order twice. Configure either automatic event discovery or explicit listener
registration for this listener, avoiding duplicate registration.

### 6. Service API (reporting / reconciliation)

```php
use yacoubalhaidari\Telr\Facades\Telr;

$xml = Telr::serviceApi()->transaction('011287290362');
$accounts = Telr::serviceApi()->accounts(); // JSON, unlike every other Service API endpoint
```

## Test Cards

> [!IMPORTANT]
> These cards are for **test mode only** and will not work for live transactions.
> Use CVV `123` for an authorised response, except American Express, which uses
> `1234`. Any other CVV returns a declined response.

Use the cards below to exercise hosted payment page integrations. Cards marked
with **3DS** open Telr's simulated 3D Secure authentication page.

| Card number           | Type             |    CVV | 3DS |
| --------------------- | ---------------- | -----: | :-: |
| `4000 0000 0000 0002` | Visa             |  `123` | No  |
| `4111 1111 1111 1111` | Visa             |  `123` | Yes |
| `4444 3333 2222 1111` | Visa             |  `123` | Yes |
| `4444 4244 4444 4440` | Visa             |  `123` | Yes |
| `4444 4444 4444 4448` | Visa             |  `123` | Yes |
| `4012 8888 8888 1881` | Visa             |  `123` | Yes |
| `5105 1051 0510 5100` | Mastercard       |  `123` | No  |
| `5454 5454 5454 5454` | Mastercard       |  `123` | Yes |
| `5555 5555 5555 4444` | Mastercard       |  `123` | Yes |
| `5555 5555 5555 5557` | Mastercard       |  `123` | Yes |
| `5581 5822 2222 2229` | Mastercard       |  `123` | Yes |
| `5641 8209 0009 7002` | Maestro UK       |  `123` | Yes |
| `3714 496353 98431`   | American Express | `1234` | No  |
| `3714 4963 5398 431`  | American Express | `1234` | No  |
| `3434 3434 3434 343`  | American Express | `1234` | No  |
| `3566 0020 2014 0006` | JCB              |  `123` | No  |
| `4464 0400 0000 0007` | MADA             |  `123` | Yes |

### Simulate Declines and Errors

In test mode, pad a Telr transaction response code with a leading `0` to make
it a three-digit CVV. For example, use `041` to simulate **Insufficient Funds**
(`D`, response code `41`).

To simulate an on-hold transaction, use CVV `999`. The transaction is
authorised but held for anti-fraud inspection; no funds are debited until the
transaction is accepted in the Merchant Administration System.

For remote invoice or other remote transaction decline simulations, contact
[Telr Support](mailto:support@telr.com).

See the [official Telr test card documentation](https://docs.telr.com/reference/test-cards)
for the full response-code reference.

## Testing

```bash
composer install
composer test
```

The suite uses fake HTTP requests and does not call a payment gateway. It covers
all DTO classes, provider/container bindings, the facade, hosted checkout,
QuickLinks, invoice XML, and authenticated webhook events. The GitHub Actions
matrix runs the supported Laravel 10–13 environments using Testbench 8–11.

For Laravel 13 specifically, use PHP 8.3+ and Testbench 11; the included PHP 8.4
CI job uses PHPUnit 13. Composer selects a compatible PHPUnit version on older
PHP versions. No application-level patches or edits inside `vendor` are needed
when installing this corrected package version.

## License

MIT
