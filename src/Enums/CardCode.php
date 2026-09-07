<?php

namespace yacoubalhaidari\Telr\Enums;

/**
 * Two-letter card identifier codes (webhook card_code / Service API card type).
 */
enum CardCode: string
{
    case VISA_CREDIT = 'VC';
    case VISA_DEBIT = 'VD';
    case VISA_ELECTRON = 'VE';
    case VISA_CORPORATE_PURCHASING = 'VP';
    case VISA_CORPORATE = 'VB';
    case MASTERCARD_CREDIT = 'MC';
    case MASTERCARD_DEBIT = 'MD';
    case MASTERCARD_MAESTRO = 'MA';
    case MADA = 'MM';
    case AMEX = 'AM';
    case AMEX_CORPORATE_PURCHASING = 'AP';
    case DINERS_CLUB = 'DN';
    case JCB = 'JC';
    case DISCOVER = 'DS';
    case APPLEPAY_VISA = 'A1';
    case APPLEPAY_MASTERCARD = 'A2';
    case APPLEPAY_AMEX = 'A3';
    case APPLEPAY_DISCOVER = 'A4';
    case APPLEPAY_JCB = 'A5';
    case APPLEPAY_MADA = 'A6';
    case SAMSUNGPAY_VISA = 'S1';
    case SAMSUNGPAY_MASTERCARD = 'S2';
    case SAMSUNGPAY_AMEX = 'S3';
    case SAMSUNGPAY_DISCOVER = 'S4';
    case SAMSUNGPAY_JCB = 'S5';
    case SAMSUNGPAY_MADA = 'S6';
    case PAYPAL = 'PP';
    case STC_PAY = 'SP';
    case TABBY = 'TB';
    case UNION_PAY = 'UP';
    case UR_PAY = 'UR';
    case JEEL_PAY = 'JP';
    case SADAD_BILL = 'ED';

    public function label(): string
    {
        return match ($this) {
            self::VISA_CREDIT => 'Visa Credit',
            self::VISA_DEBIT => 'Visa Debit',
            self::VISA_ELECTRON => 'Visa Electron',
            self::VISA_CORPORATE_PURCHASING => 'Visa Corporate Purchasing Card',
            self::VISA_CORPORATE => 'Visa Corporate',
            self::MASTERCARD_CREDIT => 'Mastercard Credit',
            self::MASTERCARD_DEBIT => 'Mastercard Debit',
            self::MASTERCARD_MAESTRO => 'Mastercard Maestro',
            self::MADA => 'Mada',
            self::AMEX => 'American Express',
            self::AMEX_CORPORATE_PURCHASING => 'American Express Corporate Purchasing Card',
            self::DINERS_CLUB => 'Diners Club',
            self::JCB => 'JCB',
            self::DISCOVER => 'Discover',
            self::APPLEPAY_VISA => 'Applepay Visa',
            self::APPLEPAY_MASTERCARD => 'Applepay Mastercard',
            self::APPLEPAY_AMEX => 'Applepay Amex',
            self::APPLEPAY_DISCOVER => 'Applepay Discover',
            self::APPLEPAY_JCB => 'Applepay JCB',
            self::APPLEPAY_MADA => 'Applepay Mada',
            self::SAMSUNGPAY_VISA => 'Samsungpay Visa',
            self::SAMSUNGPAY_MASTERCARD => 'Samsungpay Mastercard',
            self::SAMSUNGPAY_AMEX => 'Samsungpay Amex',
            self::SAMSUNGPAY_DISCOVER => 'Samsungpay Discover',
            self::SAMSUNGPAY_JCB => 'Samsungpay JCB',
            self::SAMSUNGPAY_MADA => 'Samsungpay Mada',
            self::PAYPAL => 'Paypal',
            self::STC_PAY => 'STC Pay',
            self::TABBY => 'Tabby',
            self::UNION_PAY => 'Union Pay',
            self::UR_PAY => 'UR Pay',
            self::JEEL_PAY => 'Jeel Pay',
            self::SADAD_BILL => 'Sadad Bill',
        };
    }
}
