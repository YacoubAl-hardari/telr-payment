<?php

use Illuminate\Support\Facades\Route;
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
