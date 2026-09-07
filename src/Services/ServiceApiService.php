<?php

namespace yacoubalhaidari\Telr\Services;

/**
 * Wraps the Service API documented on the "Request methods" and
 * "Transaction types" pages: https://secure.telr.com/tools/api/xml/...
 *
 * Authenticated via HTTP Basic Auth (merchant_id:api_key), configured
 * under Merchant Admin -> Integrations -> Service API. All responses
 * are raw XML strings except the /accounts/* endpoints, which are JSON.
 *
 * This service intentionally returns raw response bodies (XML string or
 * decoded array) rather than typed DTOs, since the Service API surface
 * is broad and mostly used for ad-hoc reporting/reconciliation.
 */
class ServiceApiService
{
    protected const BASE_URL = 'https://secure.telr.com/tools/api/xml';

    public function __construct(
        protected HttpClient $client,
        protected string $merchantId,
        protected string $apiKey,
    ) {
    }

    /** Most recent transactions (last 48h, max 30). */
    public function recentTransactions(): string
    {
        return $this->get('/transaction');
    }

    public function transaction(string $tranRef): string
    {
        return $this->get("/transaction/{$tranRef}");
    }

    public function linkedTransactions(string $tranRef): string
    {
        return $this->get("/transaction/{$tranRef}/linked");
    }

    public function transactionsByCartId(string $cartId): string
    {
        // Prefer the query-parameter form, which safely handles special characters.
        return $this->get('/transaction?cart_id=' . rawurlencode($cartId));
    }

    public function transactionsByEmail(string $email): string
    {
        return $this->get('/transaction/' . rawurlencode($email) . '/email');
    }

    /**
     * $cardIdentifier is either an 8-digit value (first 4 + last 4 digits
     * of the card) or a 12-digit transaction reference to match by card.
     */
    public function transactionsByCard(string $cardIdentifier): string
    {
        return $this->get("/transaction/{$cardIdentifier}/card");
    }

    /** Most recent generated reports (max 50). */
    public function reports(): string
    {
        return $this->get('/report');
    }

    public function downloadReport(string $reportId, string $file = 'file1'): string
    {
        return $this->get("/report/{$reportId}/{$file}");
    }

    /** Most recent repeat billing agreements (max 30). */
    public function recentAgreements(): string
    {
        return $this->get('/agreement');
    }

    public function agreement(string $agreementId): string
    {
        return $this->get("/agreement/{$agreementId}");
    }

    public function agreementHistory(string $agreementId): string
    {
        return $this->get("/agreement/{$agreementId}/history");
    }

    /** Cancels the agreement (DELETE method) and returns its updated details. */
    public function cancelAgreement(string $agreementId): string
    {
        return $this->client->deleteWithBasicAuth(
            self::BASE_URL . "/agreement/{$agreementId}",
            $this->merchantId,
            $this->apiKey,
        );
    }

    /** JSON response, unlike every other Service API endpoint. */
    public function accounts(): array
    {
        return json_decode($this->get('/accounts', decodeJson: true), true) ?? [];
    }

    public function accountPayouts(string $accountId): array
    {
        return json_decode($this->get("/accounts/{$accountId}/payouts", decodeJson: true), true) ?? [];
    }

    public function payoutTransactions(string $accountId, string $payoutId): array
    {
        return json_decode(
            $this->get("/accounts/{$accountId}/payouts/{$payoutId}/transactions", decodeJson: true),
            true,
        ) ?? [];
    }

    public function pendingPayouts(string $accountId): array
    {
        return json_decode($this->get("/accounts/{$accountId}/pendings", decodeJson: true), true) ?? [];
    }

    public function pendingPayoutTransactions(string $accountId): array
    {
        return json_decode(
            $this->get("/accounts/{$accountId}/pendings/transactions", decodeJson: true),
            true,
        ) ?? [];
    }

    protected function get(string $path, bool $decodeJson = false): string
    {
        return $this->client->getWithBasicAuth(self::BASE_URL . $path, $this->merchantId, $this->apiKey);
    }
}
