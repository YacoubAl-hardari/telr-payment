<?php

namespace yacoubalhaidari\Telr\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use yacoubalhaidari\Telr\Contracts\PaymentGatewayInterface;
use yacoubalhaidari\Telr\Enums\OrderStatus;

/**
 * Optional browser-return handler for Hosted Payment Page.
 *
 * Recommended application pattern (used by YacoubAlHaidari.com):
 * - Create the order with a signed, payment-specific return URL for all outcomes
 * - On return, load the local payment attempt and call checkOrderStatus
 * - Deliver the service only when OrderStatus::PAID matches cart, amount, currency, and mode
 * - Keep TelrWebhookController + transaction-advice events as the server-to-server path
 *
 * This controller covers the package-default static return URLs from config
 * (`telr.return_urls.*`). Browser query parameters alone never mark an order paid —
 * only the authenticated order.json "check" response is trusted.
 */
class TelrHostedPaymentReturnController extends Controller
{
    public function __invoke(Request $request, PaymentGatewayInterface $gateway): RedirectResponse
    {
        $ref = (string) ($request->route('ref')
            ?? $request->query('OrderRef')
            ?? $request->input('OrderRef')
            ?? '');

        if ($ref === '') {
            Log::warning('Telr return: missing order reference.');

            return $this->redirectFor(null);
        }

        try {
            $response = $gateway->checkOrderStatus($ref);
        } catch (\Throwable $exception) {
            Log::warning('Telr return: unable to verify order.', [
                'ref' => $ref,
                'error' => $exception->getMessage(),
            ]);

            return $this->redirectFor(null);
        }

        return $this->redirectFor($response->orderStatus);
    }

    protected function redirectFor(?OrderStatus $status): RedirectResponse
    {
        $urls = config('telr.return_urls', []);

        $target = match ($status) {
            OrderStatus::PAID, OrderStatus::AUTHORISED => $urls['authorised'] ?? null,
            OrderStatus::CANCELLED => $urls['cancelled'] ?? ($urls['declined'] ?? null),
            OrderStatus::DECLINED, OrderStatus::EXPIRED, OrderStatus::REPLACED => $urls['declined'] ?? null,
            default => $urls['declined'] ?? $urls['cancelled'] ?? $urls['authorised'] ?? null,
        };

        if (! is_string($target) || $target === '') {
            abort(500, 'Telr return URLs are not configured.');
        }

        return redirect()->away($target);
    }
}
