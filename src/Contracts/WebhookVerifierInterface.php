<?php

namespace yacoubalhaidari\Telr\Contracts;

interface WebhookVerifierInterface
{
    public function verifyTransaction(array $payload): bool;

    public function verifyAgreement(array $payload): bool;

    public function verifyAccount(array $payload): bool;
}
