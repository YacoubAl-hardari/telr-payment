<?php

namespace yacoubalhaidari\Telr\Services;

use yacoubalhaidari\Telr\Contracts\AgreementInterface;
use yacoubalhaidari\Telr\DTOs\Agreement\CancelledAgreementItemDTO;
use yacoubalhaidari\Telr\DTOs\Agreement\FailedAgreementItemDTO;
use yacoubalhaidari\Telr\DTOs\Agreement\ForecastAgreementItemDTO;
use yacoubalhaidari\Telr\Exceptions\TelrApiException;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Covers every repeat-billing agreement management endpoint documented:
 *
 *  - POST /gateway/ManageAgreement.json      action=changedate
 *  - POST /gateway/ManageAgreement.json      action=changedetails
 *  - POST /gateway/ManageAgreement.json      action=freeze | unfreeze
 *  - POST /gateway/forecastagreement.json
 *  - POST /gateway/failedAgreement.json
 *  - POST /gateway/cancelledAgreement.json
 */
class AgreementService implements AgreementInterface
{
    protected const MANAGE_ENDPOINT = '/gateway/ManageAgreement.json';
    protected const FORECAST_ENDPOINT = '/gateway/forecastagreement.json';
    protected const FAILED_ENDPOINT = '/gateway/failedAgreement.json';
    protected const CANCELLED_ENDPOINT = '/gateway/cancelledAgreement.json';

    public function __construct(
        protected HttpClient $client,
        protected string $store,
        protected string $authKey,
    ) {
    }

    /**
     * "Change the due date of an existing agreement": can target one or
     * more agreement IDs at once.
     */
    public function changeDueDate(array $agreementIds, string $date): array
    {
        return $this->client->postJson(self::MANAGE_ENDPOINT, [
            'ManageAgreement' => [
                'action' => 'changedate',
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'agreementids' => array_map('intval', $agreementIds),
                'date' => $date, // YYYY-MM-DD
            ],
        ]);
    }

    /**
     * "Change agreement details": pass only the fields you want updated,
     * e.g. ['cartdesc' => ..., 'amount' => ..., 'email' => ...].
     */
    public function changeDetails(string $agreementId, array $fields): array
    {
        return $this->client->postJson(self::MANAGE_ENDPOINT, [
            'ManageAgreement' => array_merge([
                'action' => 'changedetails',
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'agreementid' => $agreementId,
            ], $fields),
        ]);
    }

    /**
     * Freeze an agreement between $startDate and $endDate (YYYY-MM-DD).
     * NextScheduledDate/NextPaymentDate are set to the day after $endDate.
     */
    public function freeze(string $agreementId, string $startDate, string $endDate): array
    {
        return $this->client->postJson(self::MANAGE_ENDPOINT, [
            'ManageAgreement' => [
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'agreementid' => $agreementId,
                'action' => 'freeze',
                'startdate' => $startDate,
                'enddate' => $endDate,
            ],
        ]);
    }

    /**
     * Unfreeze an agreement. If $endDate is omitted, the agreement
     * unfreezes instantly.
     */
    public function unfreeze(string $agreementId, ?string $endDate = null): array
    {
        return $this->client->postJson(self::MANAGE_ENDPOINT, array_filter([
            'ManageAgreement' => array_filter([
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'agreementid' => $agreementId,
                'action' => 'unfreeze',
                'enddate' => $endDate,
            ]),
        ]));
    }

    /**
     * Retrieve RUNNING agreements whose next payment attempt falls within
     * the given date range. Maximum range: 31 days. $fromDate in the past
     * is silently adjusted to today by the API; $toDate must not be in
     * the past.
     *
     * @return ForecastAgreementItemDTO[]
     */
    public function forecast(string $fromDate, string $toDate): array
    {
        $this->assertMaxRangeDays($fromDate, $toDate, 31);

        $response = $this->client->postJson(self::FORECAST_ENDPOINT, [
            'ForecastAgreement' => [
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'Fromdate' => $fromDate,
                'Todate' => $toDate,
            ],
        ]);

        $body = $response['ForecastAgreementResponse'] ?? [];
        $this->assertSuccess($body, 'forecastagreement.json');

        return array_map(
            fn (array $row) => ForecastAgreementItemDTO::fromArray($row),
            $body['data'] ?? [],
        );
    }

    /**
     * Retrieve agreements in FAILED status whose most recent failure
     * event falls within the given date range. Maximum range: 31 days.
     *
     * @return FailedAgreementItemDTO[]
     */
    public function failed(string $fromDate, string $toDate): array
    {
        $this->assertMaxRangeDays($fromDate, $toDate, 31);

        $response = $this->client->postJson(self::FAILED_ENDPOINT, [
            'FailedAgreement' => [
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'Fromdate' => $fromDate,
                'Todate' => $toDate,
            ],
        ]);

        $body = $response['FailedAgreementResponse'] ?? [];
        $this->assertSuccess($body, 'failedAgreement.json');

        return array_map(
            fn (array $row) => FailedAgreementItemDTO::fromArray($row),
            $body['data'] ?? [],
        );
    }

    /**
     * Retrieve agreements cancelled within the given date range, including
     * agreements where cancellation takes effect at end of the current
     * billing period. Maximum range: 2 months.
     *
     * @return CancelledAgreementItemDTO[]
     */
    public function cancelled(string $fromDate, string $toDate): array
    {
        $this->assertMaxRangeDays($fromDate, $toDate, 62);

        $response = $this->client->postJson(self::CANCELLED_ENDPOINT, [
            'CancelledAgreement' => [
                'storeid' => $this->store,
                'authkey' => $this->authKey,
                'Fromdate' => $fromDate,
                'Todate' => $toDate,
            ],
        ]);

        $body = $response['CancelledAgreementResponse'] ?? [];
        $this->assertSuccess($body, 'cancelledAgreement.json');

        return array_map(
            fn (array $row) => CancelledAgreementItemDTO::fromArray($row),
            $body['data'] ?? [],
        );
    }

    protected function assertMaxRangeDays(string $fromDate, string $toDate, int $maxDays): void
    {
        $from = \DateTimeImmutable::createFromFormat('Y-m-d', $fromDate);
        $to = \DateTimeImmutable::createFromFormat('Y-m-d', $toDate);

        if (!$from || !$to) {
            throw new TelrValidationException('Fromdate/Todate must be in YYYY-MM-DD format.');
        }

        if ($from > $to) {
            throw new TelrValidationException('Fromdate cannot be greater than Todate.');
        }

        if ($from->diff($to)->days > $maxDays) {
            throw new TelrValidationException("The date range exceeds the maximum of {$maxDays} days.");
        }
    }

    protected function assertSuccess(array $body, string $endpoint): void
    {
        $code = (int) ($body['Code'] ?? 0);

        if ($code !== 200) {
            throw new TelrApiException(
                message: "Telr {$endpoint} returned an error: " . ($body['Status'] ?? 'Unknown error'),
                telrErrorCode: (string) $code,
                raw: $body,
            );
        }
    }
}
