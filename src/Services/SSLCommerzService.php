<?php

declare(strict_types=1);

namespace Faysal0x1\LaraPayment\Services;

use Faysal0x1\LaraPayment\Contracts\SSLCommerzContract;
use Faysal0x1\LaraPayment\Exceptions\InvalidConfigurationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SSLCommerzService implements SSLCommerzContract
{
    protected string $baseUri;
    protected array $credentials;
    protected array $config;

    public function __construct()
    {
        $this->config = config('multipayment-gateways.sslcommerz');
        $this->baseUri = $this->config['base_uri'];
        $this->credentials = [
            'store_id' => $this->config['store_id'],
            'store_password' => $this->config['store_password']
        ];
    }

    /**
     * Initialize a payment session
     */
    public function initiatePayment(array $data): array
    {
        $this->validateRequiredFields($data);

        $payload = array_merge([
            'store_id' => $this->credentials['store_id'],
            'store_passwd' => $this->credentials['store_password'],
            'tran_id' => $data['tran_id'] ?? 'TRX_' . Str::random(10),
            'success_url' => $this->config['success_url'],
            'fail_url' => $this->config['fail_url'],
            'cancel_url' => $this->config['cancel_url'],
            'ipn_url' => $this->config['ipn_url'],
            'currency' => $this->config['currency'] ?? 'BDT',
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

        $response = Http::asForm()->post($this->baseUri . '/gwprocess/v4/api.php', $payload);
        
        if (!$response->successful()) {
            throw new InvalidConfigurationException('Failed to initialize payment: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Validate IPN response
     */
    public function validateIPN(array $data): bool
    {
        $storeId = $data['store_id'] ?? '';
        $storePassword = $data['store_passwd'] ?? '';
        $tranId = $data['tran_id'] ?? '';
        $valId = $data['val_id'] ?? '';

        if ($storeId !== $this->credentials['store_id'] || $storePassword !== $this->credentials['store_password']) {
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

        $response = Http::get($validationUrl, $validationData);
        
        if (!$response->successful()) {
            return false;
        }

        $validationResponse = $response->json();
        return $validationResponse['status'] === 'VALID' && $validationResponse['tran_id'] === $tranId;
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $tranId): array
    {
        $url = $this->baseUri . '/validator/api/merchantTransIDvalidationAPI.php';
        $data = [
            'store_id' => $this->credentials['store_id'],
            'store_passwd' => $this->credentials['store_password'],
            'tran_id' => $tranId,
            'format' => 'json'
        ];

        $response = Http::get($url, $data);
        
        if (!$response->successful()) {
            throw new InvalidConfigurationException('Failed to get transaction status: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Initiate refund
     */
    public function initiateRefund(string $tranId, float $amount, string $refundReason = ''): array
    {
        $url = $this->baseUri . '/validator/api/merchantTransIDvalidationAPI.php';
        $data = [
            'store_id' => $this->credentials['store_id'],
            'store_passwd' => $this->credentials['store_password'],
            'tran_id' => $tranId,
            'refund_amount' => $amount,
            'refund_remarks' => $refundReason,
            'format' => 'json'
        ];

        $response = Http::post($url, $data);
        
        if (!$response->successful()) {
            throw new InvalidConfigurationException('Failed to initiate refund: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Validate required fields
     */
    protected function validateRequiredFields(array $data): void
    {
        $requiredFields = ['total_amount', 'product_name', 'product_category', 'cus_name', 'cus_email'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidConfigurationException("Required field '{$field}' is missing or empty");
            }
        }
    }
} 