<?php

namespace yacoubalhaidari\Telr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use yacoubalhaidari\Telr\DTOs\Webhook\AgreementWebhookDTO;

/**
 * Dispatched for any agreement webhook whose action is not CANCEL
 * (CHANGE_DATE, CHANGE_DETAILS, FREEZE, STORE_TRANSFER).
 */
class TelrAgreementChanged
{
    use Dispatchable;

    public function __construct(public readonly AgreementWebhookDTO $agreement)
    {
    }
}
