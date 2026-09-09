<?php

namespace yacoubalhaidari\Telr\DTOs;

trait FiltersPayload
{
    protected static function filter(array $data): array
    {
        return array_filter($data, static fn ($value) => $value !== null && $value !== []);
    }
}
