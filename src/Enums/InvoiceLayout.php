<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Invoice layout selection (Merchant Admin and remote XML invoice creation).
 */
enum InvoiceLayout: int
{
    case BASIC = 1;
    case UNIT_COST_QUANTITY = 2;
    case UNIT_COST_QUANTITY_DISCOUNT = 3;
    case TITLE_AND_DESCRIPTION = 4;
    case UAE_VAT_INVOICE = 5;
    case SIMPLIFIED_TAX_INVOICE = 6;
}
