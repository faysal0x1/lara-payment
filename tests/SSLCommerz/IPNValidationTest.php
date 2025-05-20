<?php

namespace Faysal0x1\LaraPayment\Tests\SSLCommerz;

use Faysal0x1\LaraPayment\Tests\TestCase;
use Faysal0x1\LaraPayment\Gateways\SSLCommerzService;

class IPNValidationTest extends TestCase
{
    protected SSLCommerzService $sslCommerz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sslCommerz = new SSLCommerzService();
        
        // Set up test configuration
        config([
            'multipayment-gateways.sslcommerz.store_id' => 'test_store_id',
            'multipayment-gateways.sslcommerz.store_password' => 'test_store_password',
        ]);
    }

    /** @test */
    public function it_validates_valid_ipn_response()
    {
        $ipnData = [
            'store_id' => 'test_store_id',
            'store_passwd' => 'test_store_password',
            'tran_id' => 'SSL_123456',
            'status' => 'VALID',
            'val_id' => '123456789',
            'amount' => '100.00',
            'currency' => 'BDT',
            'bank_tran_id' => 'BANK123456',
            'card_type' => 'VISA',
            'card_no' => '411111******1111',
            'card_issuer' => 'BRAC BANK',
            'card_brand' => 'VISA',
            'card_issuer_country' => 'Bangladesh',
            'card_issuer_country_code' => 'BD',
            'store_amount' => '100.00',
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
        ];

        $isValid = $this->sslCommerz->validateIPN($ipnData);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_rejects_invalid_store_credentials()
    {
        $ipnData = [
            'store_id' => 'wrong_store_id',
            'store_passwd' => 'wrong_password',
            'tran_id' => 'SSL_123456',
            'status' => 'VALID',
            'val_id' => '123456789',
        ];

        $isValid = $this->sslCommerz->validateIPN($ipnData);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_handles_missing_required_ipn_fields()
    {
        $ipnData = [
            'store_id' => 'test_store_id',
            'store_passwd' => 'test_store_password',
            // Missing required fields
        ];

        $isValid = $this->sslCommerz->validateIPN($ipnData);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_validates_transaction_status()
    {
        $ipnData = [
            'store_id' => 'test_store_id',
            'store_passwd' => 'test_store_password',
            'tran_id' => 'SSL_123456',
            'status' => 'INVALID_TRANSACTION',
            'val_id' => '123456789',
        ];

        $isValid = $this->sslCommerz->validateIPN($ipnData);

        $this->assertFalse($isValid);
    }
} 