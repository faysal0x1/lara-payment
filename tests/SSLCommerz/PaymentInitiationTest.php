<?php

namespace Faysal0x1\LaraPayment\Tests\SSLCommerz;

use Faysal0x1\LaraPayment\Tests\TestCase;
use Faysal0x1\LaraPayment\Gateways\SSLCommerzService;
use Illuminate\Support\Facades\Http;

class PaymentInitiationTest extends TestCase
{
    protected SSLCommerzService $sslCommerz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sslCommerz = new SSLCommerzService();
    }

    /** @test */
    public function it_can_initiate_payment()
    {
        Http::fake([
            'sandbox.sslcommerz.com/gwprocess/v4/api.php' => Http::response([
                'status' => 'VALID',
                'sessionkey' => 'test_session_key',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/gwprocess/v4/gw.php?sessionkey=test_session_key',
                'tran_id' => 'SSL_123456',
            ], 200)
        ]);

        $paymentData = [
            'total_amount' => 100,
            'currency' => 'BDT',
            'tran_id' => 'TEST_123',
            'product_category' => 'Test Category',
            'cus_name' => 'Test Customer',
            'cus_email' => 'test@example.com',
            'cus_add1' => 'Test Address',
            'cus_city' => 'Test City',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01700000000',
        ];

        $response = $this->sslCommerz->initiatePayment($paymentData);

        $this->assertIsArray($response);
        $this->assertEquals('VALID', $response['status']);
        $this->assertEquals('test_session_key', $response['sessionkey']);
        $this->assertStringContainsString('test_session_key', $response['GatewayPageURL']);
        $this->assertEquals('SSL_123456', $response['tran_id']);
    }

    /** @test */
    public function it_handles_payment_initiation_failure()
    {
        Http::fake([
            'sandbox.sslcommerz.com/gwprocess/v4/api.php' => Http::response([
                'status' => 'FAILED',
                'failedreason' => 'Invalid store credentials',
            ], 400)
        ]);

        $paymentData = [
            'total_amount' => 100,
            'currency' => 'BDT',
            'tran_id' => 'TEST_123',
        ];

        $response = $this->sslCommerz->initiatePayment($paymentData);

        $this->assertIsArray($response);
        $this->assertEquals('FAILED', $response['status']);
        $this->assertEquals('Invalid store credentials', $response['failedreason']);
    }

    /** @test */
    public function it_validates_required_payment_fields()
    {
        $this->expectException(\InvalidArgumentException::class);

        $paymentData = [
            'currency' => 'BDT',
            // Missing required fields
        ];

        $this->sslCommerz->initiatePayment($paymentData);
    }
} 