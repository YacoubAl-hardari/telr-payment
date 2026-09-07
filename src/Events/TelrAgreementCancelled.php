<?php

namespace yacoubalhaidari\Telr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use yacoubalhaidari\Telr\DTOs\Webhook\AgreementWebhookDTO;

class TelrAgreementCancelled
{
    use Dispatchable;

    public function __construct(public readonly AgreementWebhookDTO $agreement)
    {
    }
}
