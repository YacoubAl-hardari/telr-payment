<?php

namespace yacoubalhaidari\Telr\Support;

/**
 * Generates and verifies SHA1 signatures for Telr webhook payloads.
 *
 * Field order and concatenation format are taken verbatim from the
 * "Webhook" documentation page, section "Data security".
 */
class SignatureGenerator
{
    protected const TRAN_FIELDS = [
        'tran_store', 'tran_type', 'tran_class', 'tran_test', 'tran_ref',
        'tran_prevref', 'tran_firstref', 'tran_currency', 'tran_amount',
        'tran_cartid', 'tran_desc', 'tran_status', 'tran_authcode', 'tran_authmessage',
    ];

    protected const CARD_FIELDS = [
        'card_code', 'card_payment', 'bin_number', 'card_issuer', 'card_country', 'card_last4',
    ];

    protected const BILL_FIELDS = [
        'bill_title', 'bill_fname', 'bill_sname', 'bill_addr1', 'bill_addr2', 'bill_addr3',
        'bill_city', 'bill_region', 'bill_country', 'bill_zip', 'bill_email', 'bill_phone1',
    ];

    protected const AGREEMENT_FIELDS = [
        'CHANGE_DATE' => ['type', 'agreement_id', 'action', 'due_date', 'email_id', 'store_id', 'change_by', 'timestamp'],
        'CHANGE_DETAILS' => [
            'type', 'agreement_id', 'action', 'description', 'cart_id', 'customer_id',
            'subscription', 'transaction_id', 'email_id', 'phone', 'cancel_date',
            'cancellation_reason', 'cancellation_document_received', 'amount',
            'overdue_amount', 'store_id', 'change_by', 'timestamp',
        ],
        'FREEZE' => ['type', 'agreement_id', 'action', 'freeze_start_date', 'freeze_end_date', 'store_id', 'change_by', 'timestamp'],
        'STORE_TRANSFER' => ['type', 'agreement_id', 'action', 'new_store_id', 'email_id', 'store_id', 'change_by', 'timestamp'],
        'CANCEL' => ['type', 'agreement_id', 'action', 'cancellation_reason', 'cancellation_document_received', 'email_id', 'store_id', 'change_by', 'timestamp'],
    ];

    protected const ACCOUNT_FIELDS = [
        'type', 'action', 'account_id', 'store_id', 'payout_id', 'amount',
        'currency', 'date', 'payout_interval', 'payout_fee',
    ];

    public function __construct(protected string $secretKey)
    {
    }

    public function verifyTransaction(array $payload): bool
    {
        $fields = self::TRAN_FIELDS;
        // Telr inserts this field only when the merchant enables its order reference.
        if (array_key_exists('tran_order', $payload)) {
            array_splice($fields, array_search('tran_currency', $fields, true), 0, ['tran_order']);
        }

        return $this->compare($payload, $fields, $payload['tran_check'] ?? null);
    }

    public function verifyCard(array $payload): bool
    {
        return $this->compare($payload, self::CARD_FIELDS, $payload['card_check'] ?? null);
    }

    public function verifyBilling(array $payload): bool
    {
        return $this->compare($payload, self::BILL_FIELDS, $payload['bill_check'] ?? null);
    }

    public function verifyAgreement(array $payload): bool
    {
        if (isset($payload['action']) && ! is_scalar($payload['action'])) {
            return false;
        }

        $action = strtoupper((string) ($payload['action'] ?? ''));
        $fields = self::AGREEMENT_FIELDS[$action] ?? null;

        if (!$fields) {
            return false;
        }

        return $this->compare($payload, $fields, $payload['agreement_check'] ?? null);
    }

    public function verifyAccount(array $payload): bool
    {
        return $this->compare($payload, self::ACCOUNT_FIELDS, $payload['account_check'] ?? null);
    }

    /**
     * Generic signer, exposed for anyone building a payload manually
     * (e.g. constructing test fixtures).
     */
    public function sign(array $payload, array $fields): string
    {
        $parts = [$this->secretKey];

        foreach ($fields as $field) {
            $value = $payload[$field] ?? '';
            if (! is_scalar($value)) {
                throw new \InvalidArgumentException('Signature fields must be scalar values.');
            }

            // Request input is already form-decoded by PHP/Laravel. Decoding a
            // second time corrupts literal plus signs and percent sequences.
            $parts[] = trim((string) $value);
        }

        return sha1(implode(':', $parts));
    }

    protected function compare(array $payload, array $fields, mixed $expectedHash): bool
    {
        if ($this->secretKey === '' || ! is_string($expectedHash)
            || ! preg_match('/^[a-f0-9]{40}$/iD', $expectedHash)) {
            return false;
        }

        foreach ($fields as $field) {
            if (! is_scalar($payload[$field] ?? '')) {
                return false;
            }
        }

        $computed = $this->sign($payload, $fields);

        return hash_equals(strtolower($expectedHash), strtolower($computed));
    }
}
