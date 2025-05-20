<?php

namespace Faysal0x1\LaraPayment\Tests\SSLCommerz;

use Faysal0x1\LaraPayment\Tests\TestCase;
use Faysal0x1\LaraPayment\Gateways\SSLCommerzService;
use Faysal0x1\LaraPayment\Exceptions\InvalidConfigurationException;

class SSLCommerzTest extends TestCase
{
    protected SSLCommerzService $sslCommerz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sslCommerz = new SSLCommerzService();
    }

    /** @test */
    public function it_can_set_payment_gateway()
    {
        $this->sslCommerz->setPaymentGateway();
        $this->assertEquals('sslcommerz', $this->sslCommerz->paymentGateway);
    }

    /** @test */
    public function it_throws_exception_when_base_uri_is_missing()
    {
        config(['multipayment-gateways.sslcommerz.base_uri' => null]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The Base URI for `sslcommerz` is missing. Please ensure that the `base_uri` config key for `sslcommerz` is set correctly.');

        $this->sslCommerz->setBaseUri();
    }

    /** @test */
    public function it_throws_exception_when_store_credentials_are_missing()
    {
        config([
            'multipayment-gateways.sslcommerz.store_id' => null,
            'multipayment-gateways.sslcommerz.store_password' => null
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The credentials for `sslcommerz` are missing. Please ensure that the `store_id` and `store_password` config keys for `sslcommerz` are set correctly.');

        $this->sslCommerz->setSecret();
    }

    /** @test */
    public function it_can_resolve_access_token()
    {
        $this->assertEquals('', $this->sslCommerz->resolveAccessToken());
    }

    /** @test */
    public function it_can_decode_response()
    {
        $response = '{"status":"VALID","tran_id":"SSL123456","val_id":"123456789"}';
        $this->sslCommerz->response = $response;

        $decoded = $this->sslCommerz->decodeResponse();
        $this->assertIsArray($decoded);
        $this->assertEquals('VALID', $decoded['status']);
        $this->assertEquals('SSL123456', $decoded['tran_id']);
        $this->assertEquals('123456789', $decoded['val_id']);
    }
} 