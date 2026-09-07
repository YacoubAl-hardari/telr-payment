<?php

namespace yacoubalhaidari\Telr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use yacoubalhaidari\Telr\Enums\AgreementAction;
use yacoubalhaidari\Telr\Events\TelrAgreementCancelled;
use yacoubalhaidari\Telr\Events\TelrAgreementChanged;
use yacoubalhaidari\Telr\Events\TelrPayoutReceived;
use yacoubalhaidari\Telr\Events\TelrTransactionAuthorised;
use yacoubalhaidari\Telr\Events\TelrTransactionDeclined;
use yacoubalhaidari\Telr\Services\WebhookService;

/**
 * Receives the server-to-server "Transaction advice" webhook POST
 * configured under Merchant Admin -> Payment Page/Security ->
 * Transaction advice.
 *
 * Per the documentation: the receiving system must respond with HTTP 200
 * OK to indicate successful receipt. Any other response code is treated
 * as a failure and the message is retried up to 3 times, 5 seconds apart.
 * We therefore always return 200 for a *recognised but unverifiable*
 * payload (bad signature) to avoid endless retries of a request that
 * will never pass verification, while logging it for investigation.
 */
class TelrWebhookController extends Controller
{
    public function __invoke(Request $request, WebhookService $webhooks): Response
    {
        $payload = $request->all();
        $shape = $webhooks->classify($payload);

        match ($shape) {
            'transaction' => $this->handleTransaction($payload, $webhooks),
            'agreement' => $this->handleAgreement($payload, $webhooks),
            'payout' => $this->handlePayout($payload, $webhooks),
            default => Log::warning('Telr webhook: unrecognised payload shape.', ['payload' => $payload]),
        };

        return response('OK', 200);
    }

    protected function handleTransaction(array $payload, WebhookService $webhooks): void
    {
        if (!$webhooks->verifyTransaction($payload)) {
            Log::warning('Telr webhook: transaction signature mismatch.', ['tran_ref' => $payload['tran_ref'] ?? null]);

            return;
        }

        $dto = $webhooks->toTransactionDTO($payload);

        if ($dto->isAuthorised()) {
            TelrTransactionAuthorised::dispatch($dto);
        } else {
            TelrTransactionDeclined::dispatch($dto);
        }
    }

    protected function handleAgreement(array $payload, WebhookService $webhooks): void
    {
        if (!$webhooks->verifyAgreement($payload)) {
            Log::warning('Telr webhook: agreement signature mismatch.', ['agreement_id' => $payload['agreement_id'] ?? null]);

            return;
        }

        $dto = $webhooks->toAgreementDTO($payload);

        if ($dto->action === AgreementAction::CANCEL) {
            TelrAgreementCancelled::dispatch($dto);
        } else {
            TelrAgreementChanged::dispatch($dto);
        }
    }

    protected function handlePayout(array $payload, WebhookService $webhooks): void
    {
        if (!$webhooks->verifyAccount($payload)) {
            Log::warning('Telr webhook: payout signature mismatch.', ['payout_id' => $payload['payout_id'] ?? null]);

            return;
        }

        TelrPayoutReceived::dispatch($webhooks->toPayoutDTO($payload));
    }
}
