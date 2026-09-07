<?php

namespace yacoubalhaidari\Telr\Exceptions;

/**
 * Thrown when the Telr API itself returns a transport-level failure
 * (non-2xx HTTP status) or a structured error block in a successful response.
 */
class TelrApiException extends TelrException
{
    public function __construct(
        string $message,
        protected int $httpStatus = 0,
        protected ?string $telrErrorCode = null,
        protected array $raw = [],
    ) {
        parent::__construct($message);
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getTelrErrorCode(): ?string
    {
        return $this->telrErrorCode;
    }

    public function getRaw(): array
    {
        return $this->raw;
    }
}
