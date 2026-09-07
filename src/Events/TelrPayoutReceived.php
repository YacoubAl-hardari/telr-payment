<?php

namespace yacoubalhaidari\Telr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use yacoubalhaidari\Telr\DTOs\Webhook\PayoutWebhookDTO;

class TelrPayoutReceived
{
    use Dispatchable;

    public function __construct(public readonly PayoutWebhookDTO $payout)
    {
    }
}
