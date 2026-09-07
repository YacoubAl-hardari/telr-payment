<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Value for the `framed` parameter on order creation (iFrame display).
 * Requires the store to operate under HTTPS when not STANDARD.
 */
enum FramedMode: int
{
    case STANDARD = 0;
    case FRAMED_ONLY = 1;
    case FRAMED_WITH_BREAKOUT = 2;
    case FRAMED_THEN_FULL_PAGE = 3;
}
