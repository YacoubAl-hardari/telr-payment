<?php

use Illuminate\Support\Facades\Route;
use yacoubalhaidari\Telr\Http\Controllers\TelrHostedPaymentReturnController;
use yacoubalhaidari\Telr\Http\Controllers\TelrWebhookController;

/*
|--------------------------------------------------------------------------
| Telr Webhook Route
|--------------------------------------------------------------------------
|
| Registers the "Transaction advice" receiving endpoint. The path is
| configurable via config('telr.webhook_path') so it matches whatever
| URL you register in Merchant Admin -> Payment Page/Security ->
| Transaction advice. CSRF protection is excluded automatically for
| this route by adding it to VerifyCsrfToken::$except in your app,
| since it is a direct server-to-server POST with no session/cookie.
|
*/

Route::post(
    config('telr.webhook_path', 'webhooks/telr'),
    TelrWebhookController::class
)->name('telr.webhook')->withoutMiddleware(['web']);

/*
|--------------------------------------------------------------------------
| Optional Hosted Payment Page return route
|--------------------------------------------------------------------------
|
| Enabled when TELR_REGISTER_RETURN_ROUTE=true. YacoubAlHaidari.com and similar apps
| usually register their own signed payment-specific return controller instead.
|
*/

if (filter_var(config('telr.register_return_route', false), FILTER_VALIDATE_BOOLEAN)) {
    Route::match(
        ['get', 'post'],
        config('telr.return_path', 'payments/telr/return/{ref?}'),
        TelrHostedPaymentReturnController::class
    )->name('telr.return')->withoutMiddleware(['web']);
}
