<?php

namespace yacoubalhaidari\Telr\Tests\Unit;

use yacoubalhaidari\Telr\Support\SignatureGenerator;
use yacoubalhaidari\Telr\Tests\TestCase;

class SignatureGeneratorTest extends TestCase
{
    public function test_transaction_signature_matches_manually_computed_hash(): void
    {
        $secretKey = 'mysecret';
        $payload = [
            'tran_store' => '1234',
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'tran_test' => '1',
            'tran_ref' => '040035754014',
            'tran_prevref' => '040035754014',
            'tran_firstref' => '040035754014',
            'tran_currency' => 'AED',
            'tran_amount' => '10.50',
            'tran_cartid' => '12324',
            'tran_desc' => 'My purchase',
            'tran_status' => 'A',
            'tran_authcode' => '078251',
            'tran_authmessage' => 'Authorised',
        ];

        $expected = sha1(implode(':', [
            $secretKey,
            $payload['tran_store'],
            $payload['tran_type'],
            $payload['tran_class'],
            $payload['tran_test'],
            $payload['tran_ref'],
            $payload['tran_prevref'],
            $payload['tran_firstref'],
            $payload['tran_currency'],
            $payload['tran_amount'],
            $payload['tran_cartid'],
            $payload['tran_desc'],
            $payload['tran_status'],
            $payload['tran_authcode'],
            $payload['tran_authmessage'],
        ]));

        $payload['tran_check'] = $expected;

        $signer = new SignatureGenerator($secretKey);

        $this->assertTrue($signer->verifyTransaction($payload));
    }

    public function test_transaction_signature_fails_on_tampered_amount(): void
    {
        $secretKey = 'mysecret';
        $payload = [
            'tran_store' => '1234',
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'tran_test' => '1',
            'tran_ref' => '040035754014',
            'tran_prevref' => '040035754014',
            'tran_firstref' => '040035754014',
            'tran_currency' => 'AED',
            'tran_amount' => '10.50',
            'tran_cartid' => '12324',
            'tran_desc' => 'My purchase',
            'tran_status' => 'A',
            'tran_authcode' => '078251',
            'tran_authmessage' => 'Authorised',
        ];

        $signer = new SignatureGenerator($secretKey);
        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:10.50:12324:My purchase:A:078251:Authorised');

        $this->assertTrue($signer->verifyTransaction($payload));

        $payload['tran_amount'] = '100.50';

        $this->assertFalse($signer->verifyTransaction($payload));
    }

    public function test_optional_transaction_order_is_signed_before_currency(): void
    {
        $payload = $this->validSignaturePayloads()['transaction'][2];
        $payload['tran_order'] = 'ORDER-42';
        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:ORDER-42:AED:10.50:12324:My purchase:A:078251:Authorised');

        $signer = new SignatureGenerator('mysecret');

        $this->assertTrue($signer->verifyTransaction($payload));

        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:ORDER-42:10.50:12324:My purchase:A:078251:Authorised');

        $this->assertFalse($signer->verifyTransaction($payload));
    }

    public function test_empty_transaction_order_has_a_different_signature_from_an_absent_order(): void
    {
        $payload = $this->validSignaturePayloads()['transaction'][2];
        $absentOrderHash = $payload['tran_check'];
        $emptyOrderHash = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014::AED:10.50:12324:My purchase:A:078251:Authorised');
        $signer = new SignatureGenerator('mysecret');

        $this->assertTrue($signer->verifyTransaction($payload));

        $payload['tran_order'] = '';
        $this->assertFalse($signer->verifyTransaction($payload));

        $payload['tran_check'] = $emptyOrderHash;
        $this->assertTrue($signer->verifyTransaction($payload));

        unset($payload['tran_order']);
        $this->assertFalse($signer->verifyTransaction($payload));

        $payload['tran_check'] = $absentOrderHash;
        $this->assertTrue($signer->verifyTransaction($payload));
    }

    public function test_already_decoded_transaction_fields_preserve_literal_plus_and_percent_sequences(): void
    {
        $payload = $this->validSignaturePayloads()['transaction'][2];
        // PHP has already decoded the form value; these are literal characters.
        $payload['tran_desc'] = 'C++ plan %2B %20 50%';
        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:10.50:12324:C++ plan %2B %20 50%:A:078251:Authorised');
        $signer = new SignatureGenerator('mysecret');

        $this->assertTrue($signer->verifyTransaction($payload));

        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:10.50:12324:C   plan +   50%:A:078251:Authorised');

        $this->assertFalse($signer->verifyTransaction($payload));
    }

    public function test_generic_signer_preserves_already_decoded_literal_characters(): void
    {
        $signer = new SignatureGenerator('mysecret');

        $this->assertSame(
            sha1('mysecret:C++:%2B:%20:+966500000000'),
            $signer->sign([
                'description' => 'C++',
                'plus' => '%2B',
                'space' => '%20',
                'phone' => '+966500000000',
            ], ['description', 'plus', 'space', 'phone'])
        );
    }

    public function test_all_signature_types_match_independently_computed_hashes(): void
    {
        $signer = new SignatureGenerator('mysecret');

        foreach ($this->validSignaturePayloads() as $type => [$method, $signatureField, $payload]) {
            $this->assertTrue($signer->{$method}($payload), $type);

            $payload[$signatureField] = strtoupper($payload[$signatureField]);
            $this->assertTrue($signer->{$method}($payload), $type.' uppercase signature');
        }
    }

    public function test_empty_secret_rejects_even_a_matching_signature(): void
    {
        $payload = $this->validSignaturePayloads()['transaction'][2];
        $payload['tran_check'] = sha1(':1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:10.50:12324:My purchase:A:078251:Authorised');

        $this->assertFalse((new SignatureGenerator(''))->verifyTransaction($payload));

        foreach ($this->validSignaturePayloads() as $type => [$method, $signatureField, $payload]) {
            $this->assertFalse((new SignatureGenerator(''))->{$method}($payload), $type);
        }
    }

    public function test_all_signature_types_reject_missing_malformed_and_non_string_signatures(): void
    {
        $signer = new SignatureGenerator('mysecret');
        $invalidSignatures = [
            'empty' => '',
            'short' => str_repeat('a', 39),
            'long' => str_repeat('a', 41),
            'non_hexadecimal' => str_repeat('g', 40),
            'null' => null,
            'integer' => 123,
            'float' => 1.5,
            'boolean' => true,
            'array' => [],
            'nested_array' => ['signature' => str_repeat('a', 40)],
            'object' => (object) ['signature' => str_repeat('a', 40)],
        ];

        foreach ($this->validSignaturePayloads() as $type => [$method, $signatureField, $payload]) {
            $validHash = $payload[$signatureField];
            unset($payload[$signatureField]);
            $this->assertFalse($signer->{$method}($payload), $type.' missing signature');

            foreach ($invalidSignatures + [
                'leading_whitespace' => ' '.$validHash,
                'trailing_whitespace' => $validHash.' ',
                'trailing_newline' => $validHash."\n",
                'signature_array' => [$validHash],
            ] as $case => $invalidSignature) {
                $payload[$signatureField] = $invalidSignature;
                $this->assertFalse($signer->{$method}($payload), $type.' '.$case);
            }
        }
    }

    public function test_all_signature_types_reject_arrays_in_signed_fields(): void
    {
        $signer = new SignatureGenerator('mysecret');

        foreach ($this->validSignaturePayloads() as $type => [$method, $signatureField, $payload]) {
            foreach (array_diff(array_keys($payload), [$signatureField]) as $field) {
                $invalidPayload = $payload;
                $invalidPayload[$field] = [$payload[$field]];

                $this->assertFalse($signer->{$method}($invalidPayload), $type.' '.$field);
            }
        }

        $payload = $this->validSignaturePayloads()['transaction'][2];
        $payload['tran_order'] = ['ORDER-42'];
        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:ORDER-42:AED:10.50:12324:My purchase:A:078251:Authorised');

        $this->assertFalse($signer->verifyTransaction($payload));
    }

    public function test_generic_signer_rejects_an_array_field(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new SignatureGenerator('mysecret'))->sign(['tran_amount' => ['10.50']], ['tran_amount']);
    }

    public function test_all_signature_types_reject_tampered_signed_values(): void
    {
        $signer = new SignatureGenerator('mysecret');

        foreach ($this->validSignaturePayloads() as $type => [$method, $signatureField, $payload]) {
            foreach (array_diff(array_keys($payload), [$signatureField]) as $field) {
                $tamperedPayload = $payload;
                $tamperedPayload[$field] .= '-tampered';

                $this->assertFalse($signer->{$method}($tamperedPayload), $type.' '.$field);
            }
        }

        $payload = $this->validSignaturePayloads()['transaction'][2];
        $payload['tran_order'] = 'ORDER-43';
        $payload['tran_check'] = sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:ORDER-42:AED:10.50:12324:My purchase:A:078251:Authorised');

        $this->assertFalse($signer->verifyTransaction($payload));
    }

    private function validSignaturePayloads(): array
    {
        // Literal signing strings keep expectations independent of the signer's field lists.
        return [
            'transaction' => ['verifyTransaction', 'tran_check', [
                'tran_store' => '1234',
                'tran_type' => 'sale',
                'tran_class' => 'ecom',
                'tran_test' => '1',
                'tran_ref' => '040035754014',
                'tran_prevref' => '040035754014',
                'tran_firstref' => '040035754014',
                'tran_currency' => 'AED',
                'tran_amount' => '10.50',
                'tran_cartid' => '12324',
                'tran_desc' => 'My purchase',
                'tran_status' => 'A',
                'tran_authcode' => '078251',
                'tran_authmessage' => 'Authorised',
                'tran_check' => sha1('mysecret:1234:sale:ecom:1:040035754014:040035754014:040035754014:AED:10.50:12324:My purchase:A:078251:Authorised'),
            ]],
            'card' => ['verifyCard', 'card_check', [
                'card_code' => 'MC',
                'card_payment' => 'Credit',
                'bin_number' => '555555',
                'card_issuer' => 'Example Bank',
                'card_country' => 'GB',
                'card_last4' => '1234',
                'card_check' => sha1('mysecret:MC:Credit:555555:Example Bank:GB:1234'),
            ]],
            'billing' => ['verifyBilling', 'bill_check', [
                'bill_title' => 'Ms',
                'bill_fname' => 'Ada',
                'bill_sname' => 'Lovelace',
                'bill_addr1' => '1 Main Street',
                'bill_addr2' => '',
                'bill_addr3' => '',
                'bill_city' => 'London',
                'bill_region' => 'London',
                'bill_country' => 'GB',
                'bill_zip' => 'SW1A1AA',
                'bill_email' => 'ada+telr%2B@example.com',
                'bill_phone1' => '+441234567890',
                'bill_check' => sha1('mysecret:Ms:Ada:Lovelace:1 Main Street:::London:London:GB:SW1A1AA:ada+telr%2B@example.com:+441234567890'),
            ]],
            'agreement' => ['verifyAgreement', 'agreement_check', [
                'type' => 'agreement',
                'agreement_id' => 'agreement-42',
                'action' => 'CANCEL',
                'cancellation_reason' => 'Customer request',
                'cancellation_document_received' => '1',
                'email_id' => 'ada@example.com',
                'store_id' => '1234',
                'change_by' => 'merchant',
                'timestamp' => '2026-09-09T10:00:00Z',
                'agreement_check' => sha1('mysecret:agreement:agreement-42:CANCEL:Customer request:1:ada@example.com:1234:merchant:2026-09-09T10:00:00Z'),
            ]],
            'account' => ['verifyAccount', 'account_check', [
                'type' => 'account',
                'action' => 'payout',
                'account_id' => 'account-42',
                'store_id' => '1234',
                'payout_id' => 'payout-42',
                'amount' => '10.50',
                'currency' => 'AED',
                'date' => '2026-09-09',
                'payout_interval' => 'daily',
                'payout_fee' => '0.10',
                'account_check' => sha1('mysecret:account:payout:account-42:1234:payout-42:10.50:AED:2026-09-09:daily:0.10'),
            ]],
        ];
    }
}
