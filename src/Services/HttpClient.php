<?php

namespace yacoubalhaidari\Telr\Services;

use Illuminate\Support\Facades\Http;
use yacoubalhaidari\Telr\Exceptions\TelrApiException;

/**
 * Thin wrapper around Laravel's HTTP client, scoped to what the Telr
 * gateway needs: JSON POST (order.json, api_quicklink.json,
 * ManageAgreement.json, forecast/failed/cancelledAgreement.json), raw XML
 * POST (invoice_create.xml), and Basic-Auth GET/DELETE (Service API).
 */
class HttpClient
{
    public function __construct(
        protected string $baseUrl,
        protected int $timeout = 30,
    ) {
    }

    /**
     * @throws TelrApiException
     */
    public function postJson(string $endpoint, array $payload): array
    {
        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson()
            ->asJson()
            ->post($endpoint, $payload);

        if ($response->failed()) {
            throw new TelrApiException(
                message: "Telr API request to {$endpoint} failed with HTTP {$response->status()}.",
                httpStatus: $response->status(),
                raw: (array) $response->json(),
            );
        }

        return (array) $response->json();
    }

    /**
     * @throws TelrApiException
     */
    public function postXml(string $endpoint, string $xmlBody): string
    {
        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withBody($xmlBody, 'application/xml')
            ->post($endpoint);

        if ($response->failed()) {
            throw new TelrApiException(
                message: "Telr XML API request to {$endpoint} failed with HTTP {$response->status()}.",
                httpStatus: $response->status(),
            );
        }

        return $response->body();
    }

    /**
     * For the Service API, which uses HTTP Basic Authentication
     * (merchant_id:api_key, base64-encoded).
     *
     * @throws TelrApiException
     */
    public function getWithBasicAuth(string $url, string $merchantId, string $apiKey): string
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($merchantId, $apiKey)
            ->get($url);

        if ($response->failed()) {
            throw new TelrApiException(
                message: "Telr Service API request to {$url} failed with HTTP {$response->status()}.",
                httpStatus: $response->status(),
            );
        }

        return $response->body();
    }

    public function deleteWithBasicAuth(string $url, string $merchantId, string $apiKey): string
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($merchantId, $apiKey)
            ->delete($url);

        if ($response->failed()) {
            throw new TelrApiException(
                message: "Telr Service API DELETE request to {$url} failed with HTTP {$response->status()}.",
                httpStatus: $response->status(),
            );
        }

        return $response->body();
    }
}
