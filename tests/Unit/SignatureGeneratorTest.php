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
        $payload['tran_check'] = $signer->sign($payload, ['tran_store', 'tran_type']); // wrong field list on purpose

        $this->assertFalse($signer->verifyTransaction($payload));
    }
}
