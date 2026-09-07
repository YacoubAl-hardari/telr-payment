<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Actions supported by the ManageAgreement.json API and used to select
 * which signature field-list applies for agreement webhooks.
 */
enum AgreementAction: string
{
    case CHANGE_DATE = 'CHANGE_DATE';
    case CHANGE_DETAILS = 'CHANGE_DETAILS';
    case FREEZE = 'FREEZE';
    case UNFREEZE = 'unfreeze';
    case STORE_TRANSFER = 'STORE_TRANSFER';
    case CANCEL = 'CANCEL';
}
