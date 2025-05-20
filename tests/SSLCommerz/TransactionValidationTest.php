<?php

namespace Faysal0x1\LaraPayment\Tests\SSLCommerz;

use Faysal0x1\LaraPayment\Tests\TestCase;
use Faysal0x1\LaraPayment\Gateways\SSLCommerzService;
use Illuminate\Support\Facades\Http;

class TransactionValidationTest extends TestCase
{
    protected SSLCommerzService $sslCommerz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sslCommerz = new SSLCommerzService();
    }

    /** @test */
    public function it_can_validate_transaction()
    {
        Http::fake([
            'sandbox.sslcommerz.com/validator/api/validationserverAPI.php' => Http::response([
                'status' => 'VALID',
                'tran_date' => '2024-03-21 12:00:00',
                'tran_id' => 'SSL_123456',
                'val_id' => '123456789',
                'amount' => '100.00',
                'store_amount' => '100.00',
                'currency' => 'BDT',
                'bank_tran_id' => 'BANK123456',
                'card_type' => 'VISA',
                'card_no' => '411111******1111',
                'card_issuer' => 'BRAC BANK',
                'card_brand' => 'VISA',
                'card_issuer_country' => 'Bangladesh',
                'card_issuer_country_code' => 'BD',
                'currency_type' => 'BDT',
                'currency_amount' => '100.00',
                'currency_rate' => '1.0000',
                'base_fair' => '0.00',
                'value_a' => '',
                'value_b' => '',
                'value_c' => '',
                'value_d' => '',
                'risk_title' => 'Safe',
                'risk_level' => '0',
                'APIConnect' => 'DONE',
                'validated_on' => '2024-03-21 12:00:00',
                'gw_version' => '4.0',
            ], 200)
        ]);

        $response = $this->sslCommerz->validateTransaction('123456789');

        $this->assertIsArray($response);
        $this->assertEquals('VALID', $response['status']);
        $this->assertEquals('SSL_123456', $response['tran_id']);
        $this->assertEquals('123456789', $response['val_id']);
        $this->assertEquals('100.00', $response['amount']);
        $this->assertEquals('BDT', $response['currency']);
    }

    /** @test */
    public function it_handles_invalid_transaction()
    {
        Http::fake([
            'sandbox.sslcommerz.com/validator/api/validationserverAPI.php' => Http::response([
                'status' => 'INVALID_TRANSACTION',
                'failedreason' => 'Transaction not found',
            ], 404)
        ]);

        $response = $this->sslCommerz->validateTransaction('invalid_val_id');

        $this->assertIsArray($response);
        $this->assertEquals('INVALID_TRANSACTION', $response['status']);
        $this->assertEquals('Transaction not found', $response['failedreason']);
    }

    /** @test */
    public function it_handles_validation_server_error()
    {
        Http::fake([
            'sandbox.sslcommerz.com/validator/api/validationserverAPI.php' => Http::response([
                'status' => 'FAILED',
                'failedreason' => 'Internal server error',
            ], 500)
        ]);

        $response = $this->sslCommerz->validateTransaction('123456789');

        $this->assertIsArray($response);
        $this->assertEquals('FAILED', $response['status']);
        $this->assertEquals('Internal server error', $response['failedreason']);
    }
} 