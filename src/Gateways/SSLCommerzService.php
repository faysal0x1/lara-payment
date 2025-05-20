<?php

declare(strict_types=1);

namespace Faysal0x1\LaraPayment\Gateways;

use Faysal0x1\LaraPayment\Abstracts\BaseGateWay;
use Faysal0x1\LaraPayment\Exceptions\InvalidConfigurationException;

class SSLCommerzService extends BaseGateWay
{
    /**
     * The payload to initiate the transaction
     */
    protected array $payload;

    public function setPaymentGateway(): void
    {
        $this->paymentGateway = 'sslcommerz';
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function setBaseUri(): void
    {
        $baseUri = config('multipayment-gateways.sslcommerz.base_uri');

        if (! $baseUri) {
            throw new InvalidConfigurationException("The Base URI for `{$this->paymentGateway}` is missing. Please ensure that the `base_uri` config key for `{$this->paymentGateway}` is set correctly.");
        }

        $this->baseUri = $baseUri;
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function setSecret(): void
    {
        $storeId = config('multipayment-gateways.sslcommerz.store_id');
        $storePassword = config('multipayment-gateways.sslcommerz.store_password');

        if (! $storeId || ! $storePassword) {
            throw new InvalidConfigurationException("The credentials for `{$this->paymentGateway}` are missing. Please ensure that the `store_id` and `store_password` config keys for `{$this->paymentGateway}` are set correctly.");
        }

        $this->secret = [
            'store_id' => $storeId,
            'store_password' => $storePassword
        ];
    }

    /**
     * Set the access token for the request
     */
    public function resolveAccessToken(): string
    {
        return '';
    }

    /**
     * Decode the response
     */
    public function decodeResponse(): array
    {
        return json_decode($this->response, true);
    }

    /**
     * Initialize a payment session
     */
    public function initiatePayment(array $data): array
    {
        $payload = array_merge([
            'store_id' => $this->secret['store_id'],
            'store_passwd' => $this->secret['store_password'],
            'tran_id' => uniqid('SSL_'),
            'success_url' => config('multipayment-gateways.sslcommerz.success_url'),
            'fail_url' => config('multipayment-gateways.sslcommerz.fail_url'),
            'cancel_url' => config('multipayment-gateways.sslcommerz.cancel_url'),
            'ipn_url' => config('multipayment-gateways.sslcommerz.ipn_url'),
            'currency' => 'BDT',
            'product_category' => 'general',
            'emi_option' => 0,
            'cus_add1' => '',
            'cus_add2' => '',
            'cus_city' => '',
            'cus_state' => '',
            'cus_postcode' => '',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '',
            'cus_email' => '',
            'cus_fax' => '',
            'shipping_method' => 'NO',
            'ship_name' => '',
            'ship_add1' => '',
            'ship_add2' => '',
            'ship_city' => '',
            'ship_state' => '',
            'ship_postcode' => '',
            'ship_country' => 'Bangladesh',
        ], $data);

        $response = $this->httpClient()->post('/gwprocess/v4/api.php', $payload);
        return $this->decodeResponse();
    }

    /**
     * Validate IPN response
     */
    public function validateIPN(array $data): bool
    {
        $storeId = $data['store_id'] ?? '';
        $storePassword = $data['store_passwd'] ?? '';
        $tranId = $data['tran_id'] ?? '';
        $status = $data['status'] ?? '';
        $valId = $data['val_id'] ?? '';

        if ($storeId !== $this->secret['store_id'] || $storePassword !== $this->secret['store_password']) {
            return false;
        }

        $validationUrl = $this->baseUri . '/validator/api/validationserverAPI.php';
        $validationData = [
            'val_id' => $valId,
            'store_id' => $storeId,
            'store_passwd' => $storePassword,
            'v' => 1,
            'format' => 'json'
        ];

        $response = $this->httpClient()->get($validationUrl, $validationData);
        $validationResponse = $this->decodeResponse();

        return $validationResponse['status'] === 'VALID' && $validationResponse['tran_id'] === $tranId;
    }
} 