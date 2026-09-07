<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Repeat billing period unit used across QuickLink, Invoice repeat billing,
 * and the ManageAgreement APIs.
 */
enum BillingPeriod: string
{
    case WEEKLY = 'W';
    case MONTHLY = 'M';
    case SEMI_WEEKLY = 'S';
    case TWICE_MONTHLY = 'T';
}
