<?php

namespace yacoubalhaidari\Telr\DTOs;

/**
 * A top-level API request needs credentials to produce its wire payload.
 * Nested payload DTOs instead implement BaseDTO::toArray() without arguments.
 */
abstract class AuthenticatedRequestDTO
{
    use FiltersPayload;

    abstract public function toArray(string $store, string $credential): array;
}
