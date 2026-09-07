<?php

namespace yacoubalhaidari\Telr\Exceptions;

/**
 * Thrown when a webhook payload's SHA1 signature (tran_check / card_check /
 * bill_check / agreement_check / account_check) does not match the
 * computed value. Treat any request that raises this as untrusted.
 */
class TelrSignatureMismatchException extends TelrException
{
}
