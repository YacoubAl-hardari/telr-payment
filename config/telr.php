<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hosted Payment Page / QuickLink / ManageAgreement credentials
    |--------------------------------------------------------------------------
    | Found in Merchant Admin -> Integrations -> Hosted Payment Page ->
    | Configuration.
    */
    'store_id' => env('TELR_STORE_ID'),
    'auth_key' => env('TELR_AUTH_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Webhook secret key
    |--------------------------------------------------------------------------
    | Used to verify tran_check / card_check / bill_check / agreement_check /
    | account_check SHA1 signatures on incoming webhook payloads.
    */
    'secret_key' => env('TELR_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Remote invoicing password
    |--------------------------------------------------------------------------
    | Created under the Remote section of the invoice configuration page.
    | Required for invoice_create.xml requests.
    */
    'invoice_password' => env('TELR_INVOICE_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Service API credentials (HTTP Basic Auth)
    |--------------------------------------------------------------------------
    | Generated under Merchant Admin -> Integrations -> Service API.
    | merchant_id is shown when viewing an existing API key.
    */
    'service_api' => [
        'merchant_id' => env('TELR_SERVICE_API_MERCHANT_ID'),
        'api_key' => env('TELR_SERVICE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway base URL / behaviour
    |--------------------------------------------------------------------------
    */
    'base_url' => env('TELR_BASE_URL', 'https://secure.telr.com'),
    'timeout' => env('TELR_TIMEOUT', 30),
    'test_mode' => env('TELR_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Default return URLs for order.json / Hosted Payment Page
    |--------------------------------------------------------------------------
    */
    'return_urls' => [
        'authorised' => env('TELR_RETURN_AUTHORISED'),
        'declined' => env('TELR_RETURN_DECLINED'),
        'cancelled' => env('TELR_RETURN_CANCELLED'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook route path
    |--------------------------------------------------------------------------
    | Must match the "Transaction advice" URL configured in Merchant Admin.
    | Registered without the 'web' middleware group since this is a direct
    | server-to-server POST with no browser/session involved.
    */
    'webhook_path' => env('TELR_WEBHOOK_PATH', 'webhooks/telr'),
];
