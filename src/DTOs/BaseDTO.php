<?php

namespace yacoubalhaidari\Telr\DTOs;

abstract class BaseDTO
{
    use FiltersPayload;

    /**
     * Convert the DTO to its wire-format array, ready to be merged into
     * an API request payload. Implementations should strip null values
     * so optional fields are omitted rather than sent empty.
     */
    abstract public function toArray(): array;
}
