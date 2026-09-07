<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Order status codes as documented alongside the "Invalid Request" response.
 */
enum OrderStatus: int
{
    case PENDING = 1;
    case AUTHORISED = 2;
    case PAID = 3;
    case PAYMENT_REQUESTED = 4;
    case EXPIRED = -1;
    case CANCELLED = -2;
    case DECLINED = -3;
    case REPLACED = -4;
}
