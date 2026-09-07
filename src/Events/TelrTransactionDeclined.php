<?php

namespace yacoubalhaidari\Telr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use yacoubalhaidari\Telr\DTOs\Webhook\TransactionWebhookDTO;

class TelrTransactionDeclined
{
    use Dispatchable;

    public function __construct(public readonly TransactionWebhookDTO $transaction)
    {
    }
}
