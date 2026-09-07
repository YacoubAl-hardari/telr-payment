<?php

namespace yacoubalhaidari\Telr\Tests\Feature;

use yacoubalhaidari\Telr\Tests\TestCase;

class WebhookTest extends TestCase
{
    public function test_webhook_endpoint_returns_200_for_a_recognised_payload(): void
    {
        $secretKey = 'test-secret-key';

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

        $check = sha1(implode(':', array_merge([$secretKey], array_values($payload))));
        $payload['tran_check'] = $check;

        $response = $this->postJson('/webhooks/telr', $payload);

        $response->assertStatus(200);
    }
}
