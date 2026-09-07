<?php

namespace yacoubalhaidari\Telr\Services;

use yacoubalhaidari\Telr\Contracts\WebhookVerifierInterface;
use yacoubalhaidari\Telr\DTOs\Webhook\AgreementWebhookDTO;
use yacoubalhaidari\Telr\DTOs\Webhook\PayoutWebhookDTO;
use yacoubalhaidari\Telr\DTOs\Webhook\TransactionWebhookDTO;
use yacoubalhaidari\Telr\Support\SignatureGenerator;

/**
 * Verifies and classifies an incoming Telr webhook POST body.
 *
 * Per the "Webhook" documentation, three payload shapes can arrive at
 * the configured Transaction Advice URL: transaction-related, agreement-
 * related, and payout-related (type=ACCOUNT, action=PAYOUT).
 */
class WebhookService implements WebhookVerifierInterface
{
    public function __construct(protected SignatureGenerator $signer)
    {
    }

    public function verifyTransaction(array $payload): bool
    {
        return $this->signer->verifyTransaction($payload);
    }

    public function verifyAgreement(array $payload): bool
    {
        return $this->signer->verifyAgreement($payload);
    }

    public function verifyAccount(array $payload): bool
    {
        return $this->signer->verifyAccount($payload);
    }

    /**
     * Classifies the payload shape without verifying signatures.
     * 'transaction' | 'agreement' | 'payout' | 'unknown'.
     */
    public function classify(array $payload): string
    {
        if (isset($payload['tran_ref'])) {
            return 'transaction';
        }

        if (($payload['type'] ?? null) === 'ACCOUNT' && ($payload['action'] ?? null) === 'PAYOUT') {
            return 'payout';
        }

        if (isset($payload['agreement_id'])) {
            return 'agreement';
        }

        return 'unknown';
    }

    public function toTransactionDTO(array $payload): TransactionWebhookDTO
    {
        return TransactionWebhookDTO::fromPayload($payload);
    }

    public function toAgreementDTO(array $payload): AgreementWebhookDTO
    {
        return AgreementWebhookDTO::fromPayload($payload);
    }

    public function toPayoutDTO(array $payload): PayoutWebhookDTO
    {
        return PayoutWebhookDTO::fromPayload($payload);
    }
}
