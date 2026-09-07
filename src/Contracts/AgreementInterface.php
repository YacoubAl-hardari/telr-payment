<?php

namespace yacoubalhaidari\Telr\Contracts;

interface AgreementInterface
{
    public function changeDueDate(array $agreementIds, string $date): array;

    public function changeDetails(string $agreementId, array $fields): array;

    public function freeze(string $agreementId, string $startDate, string $endDate): array;

    public function unfreeze(string $agreementId, ?string $endDate = null): array;

    public function forecast(string $fromDate, string $toDate): array;

    public function failed(string $fromDate, string $toDate): array;

    public function cancelled(string $fromDate, string $toDate): array;
}
