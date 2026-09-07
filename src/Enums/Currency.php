<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Supported currency codes. Actual availability depends on the merchant's
 * acquiring bank accounts — this enum only documents the gateway-wide list.
 */
enum Currency: string
{
    case AED = 'AED';
    case BHD = 'BHD';
    case CAD = 'CAD';
    case EGP = 'EGP';
    case EUR = 'EUR';
    case IDR = 'IDR';
    case GBP = 'GBP';
    case JOD = 'JOD';
    case JPY = 'JPY';
    case KHR = 'KHR';
    case KWD = 'KWD';
    case MYR = 'MYR';
    case OMR = 'OMR';
    case PHP = 'PHP';
    case QAR = 'QAR';
    case SAR = 'SAR';
    case SGD = 'SGD';
    case THB = 'THB';
    case USD = 'USD';
    case VND = 'VND';
    case INR = 'INR';
}
