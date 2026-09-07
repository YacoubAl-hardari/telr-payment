<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Full response code table from the "Response codes" documentation page.
 * Backed by the numeric code; message() returns the documented text.
 */
enum ResponseCode: int
{
    case INVALID_REQUEST = 1;
    case COST_OR_CURRENCY_NOT_SUPPLIED = 2;
    case CART_ID_NOT_SET = 3;
    case INVALID_STORE_ID = 4;
    case COST_OR_CURRENCY_NOT_VALID = 5;
    case INVALID_TRANSACTION_MODE = 6;
    case CARD_EXPIRY_NOT_SUPPLIED = 7;
    case CARD_START_DATE_NOT_SUPPLIED = 8;
    case CARD_ISSUE_NUMBER_NOT_SUPPLIED = 9;
    case CARD_NUMBER_NOT_SUPPLIED = 10;
    case INVALID_CARD_NUMBER = 11;
    case CARD_EXPIRED = 12;
    case CARD_TYPE_MISMATCH = 14;
    case INVALID_CVV = 15;
    case CVV_NOT_SUPPLIED = 16;
    case NAME_NOT_VALID = 17;
    case ADDRESS_NOT_VALID = 18;
    case COUNTRY_NOT_VALID = 19;
    case IP_ADDRESS_NOT_VALID = 20;
    case CARD_CURRENCY_CLASS_NOT_SUPPORTED = 21;
    case INVALID_TRANSACTION_REFERENCE = 22;
    case AMOUNT_DIFFERS_FROM_ORIGINAL = 23;
    case CURRENCY_DIFFERS_FROM_ORIGINAL = 24;
    case ORIGINAL_TRANSACTION_NOT_AUTHORISED = 25;
    case ORIGINAL_TRANSACTION_ALREADY_VOIDED = 26;
    case ORIGINAL_TRANSACTION_MISMATCH = 27;
    case INVALID_START_DATE = 28;
    case AMOUNT_GREATER_THAN_AVAILABLE_BALANCE = 29;
    case CARD_DETAILS_DIFFER_FROM_ORIGINAL = 30;
    case NOT_AUTHORISED = 31;
    case ORIGINAL_TRANSACTION_CANNOT_BE_VOIDED = 32;
    case CANCELLED = 33;
    case NO_RESPONSE = 34;
    case UNABLE_TO_REFUND = 35;
    case PREVIOUS_TRANSACTION_ON_HOLD = 36;
    case BLOCKED_BY_ACQUIRER = 37;
    case INVALID_EXPIRY_DATE = 38;
    case INVALID_TRANSACTION_CLASS = 39;
    case INVALID_TRANSACTION_TYPE = 40;
    case INSUFFICIENT_FUNDS = 41;
    case CVV_MISMATCH = 42;
    case EMAIL_NOT_VALID = 43;
    case PHONE_NOT_VALID = 44;
    case TRANSACTION_MODE_DIFFERS_FROM_ORIGINAL = 45;
    case THREE_DS_NOT_AVAILABLE = 46;
    case THREE_DS_REJECTED = 47;
    case DESCRIPTION_NOT_SET = 48;
    case SOLD_OUT = 49;
    case CARD_ATM_ONLY = 50;
    case INVALID_TRANSACTION_METHOD = 52;
    case AUTHORISATION_EXPIRED = 53;
    case TRANSACTION_PART_NOT_SPECIFIED = 54;
    case UNABLE_TO_ACCESS_TRANSACTION_PART = 55;
    case DUPLICATE_TRANSACTION = 56;
    case CONTINUOUS_AUTHORITY_NOT_AVAILABLE = 57;
    case ERROR_CONNECTING_TO_SERVICE_PROVIDER = 58;
    case REQUEST_ABORTED = 59;
    case VERIFICATION_FAILED = 60;
    case REFER_TO_CARD_ISSUER = 61;
    case DO_NOT_HONOR = 62;
    case AVS_MISMATCH = 63;
    case CVV_AND_AVS_MISMATCH = 64;
    case CARD_NOT_ENABLED_FOR_ECOMMERCE = 65;
    case CARD_CANCELLED = 66;
    case TRANSACTION_NOT_PERMITTED_BY_ISSUER = 67;
    case NO_INVALID_ACCOUNT = 68;
    case REFUND_MANUAL_BANK = 69;
    case REFUND_CANNOT_BE_PROCESSED = 70;
    case INVALID_TERMINAL_CONFIGURATIONS = 71;
    case INVALID_SPLIT_PARAMETER = 72;
    case SPLIT_PAYMENT_NOT_ENABLED = 73;
    case CARD_TOKEN_VALIDATION_FAILED = 74;
    case STCPAY_ORDER_CREATION_FAILED = 75;
    case PAYPAL_ORDER_CREATION_FAILED = 76;
    case CYBERSOURCE_CONFIG_PROBLEM = 77;
    case TIMEOUT_AT_PAYMENT_PROCESSOR = 78;
    case ANTI_FRAUD_PRE_AUTH = 90;
    case FRAUD_DETECTED_BY_ISSUER = 91;
    case ANTI_FRAUD_POST_AUTH = 92;
    case CARD_LIMITS_EXCEEDED = 93;
    case TERMINAL_LIMITS = 94;
    case NOT_AUTHORISED_2 = 95;
    case FRAUD_DETECTED_BY_ACQUIRER = 96;
    case MULTIPLE_EMAIL_SAME_CARD = 97;
    case INTERNAL_SYSTEM_ERROR = 98;
    case UNKNOWN_ERROR = 99;
    case INVALID_END_DATE = 100;
    case LIMIT_VALUE_TOO_HIGH = 101;
    case DATE_RANGE_EXCEEDS_SEVEN_DAYS = 102;
    case INVALID_OR_LESS_PARAMETERS = 103;
    case FROM_DATE_GREATER_THAN_TO_DATE = 104;
    case TRANSACTION_ALREADY_CAPTURED = 105;
    case INVALID_PATH_PARAMETER = 106;
    case ACQUIRER_SYSTEM_ERROR = 107;
    case INVALID_MERCHANT = 108;
    case INVALID_TRANSACTION = 109;
    case INVALID_ISSUER = 110;
    case RESTRICTED = 111;
    case CLOSED_CARD_ACCOUNT = 112;
    case AUTHORISATION_REVOKED = 113;
    case FRAUD_SUSPECTED_BY_ISSUER = 114;
    case FRAUD_SUSPECTED_BY_SCHEME = 115;
    case NO_INVALID_PIN = 116;
    case SCHEME_LIFECYCLE_POLICY = 117;
    case UNABLE_TO_ROUTE = 118;
    case NO_INVALID_OTP = 119;
    case OTP_LIMIT_EXCEEDED = 120;
    case INACTIVE_ACCOUNT = 121;
    case ALREADY_PAID = 122;
    case PAYMENT_EXPIRED = 123;
    case ACCOUNT_LIMIT_EXCEEDED = 124;

    public function message(): string
    {
        return match ($this) {
            self::INVALID_REQUEST => 'Invalid request',
            self::COST_OR_CURRENCY_NOT_SUPPLIED => 'Transaction cost or currency not supplied',
            self::CART_ID_NOT_SET => 'Cart ID not set',
            self::INVALID_STORE_ID => 'Invalid store ID',
            self::COST_OR_CURRENCY_NOT_VALID => 'Transaction cost or currency not valid',
            self::INVALID_TRANSACTION_MODE => 'Invalid transaction mode',
            self::CARD_EXPIRY_NOT_SUPPLIED => 'Card expiry not supplied',
            self::CARD_START_DATE_NOT_SUPPLIED => 'Card start date not supplied',
            self::CARD_ISSUE_NUMBER_NOT_SUPPLIED => 'Card issue number not supplied',
            self::CARD_NUMBER_NOT_SUPPLIED => 'Card number not supplied',
            self::INVALID_CARD_NUMBER => 'Invalid card number',
            self::CARD_EXPIRED => 'Card expired',
            self::CARD_TYPE_MISMATCH => 'Card type mismatch',
            self::INVALID_CVV => 'Invalid card security code (CVV)',
            self::CVV_NOT_SUPPLIED => 'Card security code (CVV) not supplied',
            self::NAME_NOT_VALID => 'Name not valid/not supplied',
            self::ADDRESS_NOT_VALID => 'Address not valid/not supplied',
            self::COUNTRY_NOT_VALID => 'Country not valid/not supplied',
            self::IP_ADDRESS_NOT_VALID => 'IP address not valid/not supplied',
            self::CARD_CURRENCY_CLASS_NOT_SUPPORTED => 'Card/Currency/Class combination not supported',
            self::INVALID_TRANSACTION_REFERENCE => 'Invalid transaction reference',
            self::AMOUNT_DIFFERS_FROM_ORIGINAL => 'Amount differs from original',
            self::CURRENCY_DIFFERS_FROM_ORIGINAL => 'Currency differs from original',
            self::ORIGINAL_TRANSACTION_NOT_AUTHORISED => 'Original transaction not authorised',
            self::ORIGINAL_TRANSACTION_ALREADY_VOIDED => 'Original transaction already voided',
            self::ORIGINAL_TRANSACTION_MISMATCH => 'Original transaction mismatch',
            self::INVALID_START_DATE => 'Invalid start date',
            self::AMOUNT_GREATER_THAN_AVAILABLE_BALANCE => 'Amount greater than available balance',
            self::CARD_DETAILS_DIFFER_FROM_ORIGINAL => 'Card details differ from original',
            self::NOT_AUTHORISED => 'Not authorised',
            self::ORIGINAL_TRANSACTION_CANNOT_BE_VOIDED => 'Original transaction cannot be voided',
            self::CANCELLED => 'Cancelled',
            self::NO_RESPONSE => 'No response',
            self::UNABLE_TO_REFUND => 'Unable to refund',
            self::PREVIOUS_TRANSACTION_ON_HOLD => 'Previous transaction is on hold',
            self::BLOCKED_BY_ACQUIRER => 'Blocked by acquirer',
            self::INVALID_EXPIRY_DATE => 'Invalid expiry date',
            self::INVALID_TRANSACTION_CLASS => 'Invalid transaction class',
            self::INVALID_TRANSACTION_TYPE => 'Invalid transaction type',
            self::INSUFFICIENT_FUNDS => 'Insufficient funds',
            self::CVV_MISMATCH => 'Card security code (CVV) mismatch',
            self::EMAIL_NOT_VALID => 'Email not valid/not supplied',
            self::PHONE_NOT_VALID => 'Phone number not valid/not supplied',
            self::TRANSACTION_MODE_DIFFERS_FROM_ORIGINAL => 'Transaction mode differs from original',
            self::THREE_DS_NOT_AVAILABLE => '3DSecure authentication not available for this card',
            self::THREE_DS_REJECTED => '3DSecure authentication rejected',
            self::DESCRIPTION_NOT_SET => 'Description not set',
            self::SOLD_OUT => 'Sold out',
            self::CARD_ATM_ONLY => 'Card is for ATM use only',
            self::INVALID_TRANSACTION_METHOD => 'Invalid Transaction Method',
            self::AUTHORISATION_EXPIRED => 'Authorisation expired',
            self::TRANSACTION_PART_NOT_SPECIFIED => 'Transaction part not specified',
            self::UNABLE_TO_ACCESS_TRANSACTION_PART => 'Unable to access transaction part',
            self::DUPLICATE_TRANSACTION => 'Duplicate transaction',
            self::CONTINUOUS_AUTHORITY_NOT_AVAILABLE => 'Continuous authority not available on referenced transaction',
            self::ERROR_CONNECTING_TO_SERVICE_PROVIDER => 'Error connecting to service provider',
            self::REQUEST_ABORTED => 'Request aborted',
            self::VERIFICATION_FAILED => 'Verification failed',
            self::REFER_TO_CARD_ISSUER => 'Refer to card issuer',
            self::DO_NOT_HONOR => 'Do not honor',
            self::AVS_MISMATCH => 'Address verification (AVS) mismatch',
            self::CVV_AND_AVS_MISMATCH => 'Card security code (CVV) and address (AVS) mismatch',
            self::CARD_NOT_ENABLED_FOR_ECOMMERCE => 'Card is not enabled for e-commerce',
            self::CARD_CANCELLED => 'Card cancelled',
            self::TRANSACTION_NOT_PERMITTED_BY_ISSUER => 'Transaction not permitted by issuer',
            self::NO_INVALID_ACCOUNT => 'No/invalid account',
            self::REFUND_MANUAL_BANK => 'Refund needs to be completed manually by the bank',
            self::REFUND_CANNOT_BE_PROCESSED => 'Currently refund cannot be processed, please reach out to client experience',
            self::INVALID_TERMINAL_CONFIGURATIONS => 'Invalid Terminal configurations',
            self::INVALID_SPLIT_PARAMETER => 'Invalid value for split parameter',
            self::SPLIT_PAYMENT_NOT_ENABLED => 'Split Payment not enabled for the store',
            self::CARD_TOKEN_VALIDATION_FAILED => 'Card token validation failed',
            self::STCPAY_ORDER_CREATION_FAILED => 'STCPay order creation failed',
            self::PAYPAL_ORDER_CREATION_FAILED => 'Paypal order creation failed',
            self::CYBERSOURCE_CONFIG_PROBLEM => 'Problem with Cybersource configurations',
            self::TIMEOUT_AT_PAYMENT_PROCESSOR => 'Timeout at payment processor',
            self::ANTI_FRAUD_PRE_AUTH => 'Anti-fraud pre-auth',
            self::FRAUD_DETECTED_BY_ISSUER => 'Fraud detected by issuer',
            self::ANTI_FRAUD_POST_AUTH => 'Anti-fraud post-auth',
            self::CARD_LIMITS_EXCEEDED => 'Card limits exceeded',
            self::TERMINAL_LIMITS => 'Terminal limits',
            self::NOT_AUTHORISED_2 => 'Not authorised',
            self::FRAUD_DETECTED_BY_ACQUIRER => 'Fraud detected by acquirer',
            self::MULTIPLE_EMAIL_SAME_CARD => 'Multiple email used with same card',
            self::INTERNAL_SYSTEM_ERROR => 'Internal system error',
            self::UNKNOWN_ERROR => 'Unknown error',
            self::INVALID_END_DATE => 'Invalid end date',
            self::LIMIT_VALUE_TOO_HIGH => 'Limit value should be less than or equal to 4000',
            self::DATE_RANGE_EXCEEDS_SEVEN_DAYS => 'The date range exceeds seven days',
            self::INVALID_OR_LESS_PARAMETERS => 'Invalid or Less parameters in URL',
            self::FROM_DATE_GREATER_THAN_TO_DATE => 'From date can not be greater than to date',
            self::TRANSACTION_ALREADY_CAPTURED => 'Transaction is already captured',
            self::INVALID_PATH_PARAMETER => 'Invalid path parameter',
            self::ACQUIRER_SYSTEM_ERROR => 'Acquirer system error',
            self::INVALID_MERCHANT => 'Invalid merchant',
            self::INVALID_TRANSACTION => 'Invalid transaction',
            self::INVALID_ISSUER => 'Invalid issuer',
            self::RESTRICTED => 'Restricted',
            self::CLOSED_CARD_ACCOUNT => 'Closed card/account',
            self::AUTHORISATION_REVOKED => 'Authorisation revoked',
            self::FRAUD_SUSPECTED_BY_ISSUER => 'Fraud suspected by issuer',
            self::FRAUD_SUSPECTED_BY_SCHEME => 'Fraud suspected by scheme',
            self::NO_INVALID_PIN => 'No/invalid pin',
            self::SCHEME_LIFECYCLE_POLICY => 'Scheme lifecycle/policy',
            self::UNABLE_TO_ROUTE => 'Unable to route',
            self::NO_INVALID_OTP => 'No/invalid OTP',
            self::OTP_LIMIT_EXCEEDED => 'OTP limit exceeded',
            self::INACTIVE_ACCOUNT => 'Inactive account',
            self::ALREADY_PAID => 'Already paid',
            self::PAYMENT_EXPIRED => 'Payment expired',
            self::ACCOUNT_LIMIT_EXCEEDED => 'Account limit exceeded',
        };
    }

    /**
     * VISA: these codes must never be retried for recurring transactions.
     */
    public function isVisaNoRetry(): bool
    {
        return in_array($this->value, [11, 66, 67, 91, 109, 110, 112, 113], true);
    }

    /**
     * Mastercard: these codes must never be retried for recurring transactions.
     */
    public function isMastercardNoRetry(): bool
    {
        return in_array($this->value, [11, 12, 66, 91, 110], true);
    }
}
