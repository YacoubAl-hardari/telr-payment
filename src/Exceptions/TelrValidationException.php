<?php

namespace yacoubalhaidari\Telr\Exceptions;

/**
 * Thrown when a DTO or request payload fails local validation
 * before it is ever sent to the Telr API (e.g. field length, max counts).
 */
class TelrValidationException extends TelrException
{
}
