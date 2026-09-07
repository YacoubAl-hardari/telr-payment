<?php

namespace yacoubalhaidari\Telr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use yacoubalhaidari\Telr\DTOs\Webhook\TransactionWebhookDTO;

class TelrTransactionAuthorised
{
    use Dispatchable;

    public function __construct(public readonly TransactionWebhookDTO $transaction)
    {
    }
}
