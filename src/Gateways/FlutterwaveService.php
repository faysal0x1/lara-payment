<?php

declare(strict_types=1);

namespace Faysal0x1\LaravelMultipaymentGateways\Gateways;

use Faysal0x1\LaravelMultipaymentGateways\Abstracts\BaseGateWay;
use Faysal0x1\LaravelMultipaymentGateways\Contracts\FlutterwaveContract;
use Faysal0x1\LaravelMultipaymentGateways\Exceptions\InvalidConfigurationException;
use Faysal0x1\LaravelMultipaymentGateways\Traits\ConsumesExternalServices;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\BankTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\ChargeTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\OtpTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\PaymentPlanTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\SettlementTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\SubscriptionTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\TransactionTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\TransferBeneficiaryTrait;
use Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave\TransferTrait;

class FlutterwaveService extends BaseGateWay implements FlutterwaveContract
{
    use BankTrait,
        ChargeTrait,
        ConsumesExternalServices,
        OtpTrait,
        PaymentPlanTrait,
        SettlementTrait,
        SubscriptionTrait,
        TransactionTrait,
        TransferBeneficiaryTrait,
        TransferTrait;

    /**
     * The redirect url to consume the Flutterwave's service
     */
    protected string $redirectUrl;

    /**
     * The payload to initiate the transaction
     */
    protected array $payload;

    /**
     * The encryption key to encrypt payload for direct card charge
     */
    protected string $encryptionKey;

    public function __construct()
    {
        parent::__construct();
        $this->setEncryptionKey();
    }

    /**
     * Set the payment gateway for the class
     */
    public function setPaymentGateway(): void
    {
        $this->paymentGateway = 'flutterwave';
    }

    /**
     * Set the encryption key for the class
     */
    public function setEncryptionKey(): void
    {
        $encryptionKey = config('multipayment-gateways.flutterwave.encryption_key');

        if (! $encryptionKey) {
            return;
        }

        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Set the base URI for the API request
     *
     * @throws InvalidConfigurationException
     */
    public function setBaseUri(): void
    {
        $baseUri = config('multipayment-gateways.flutterwave.base_uri');

        if (! $baseUri) {
            throw new InvalidConfigurationException("The Base URI for `{$this->paymentGateway}` is missing. Please ensure that the `base_uri` config key for `{$this->paymentGateway}` is set correctly.");
        }

        $this->baseUri = $baseUri;
    }

    /**
     * Set the secret key for the API request
     *
     * @throws InvalidConfigurationException
     */
    public function setSecret(): void
    {
        $secret = config('multipayment-gateways.flutterwave.secret');

        if (! $secret) {
            throw new InvalidConfigurationException("The secret key for `{$this->paymentGateway}` is missing. Please ensure that the `secret` config key for `{$this->paymentGateway}` is set correctly.");
        }

        $this->secret = $secret;
    }

    /**
     * Resolve the authorization URL / Endpoint
     */
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers): void
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    /**
     * Set the access token for the request
     */
    public function resolveAccessToken(): string
    {
        return "Bearer {$this->secret}";
    }

    /**
     * Decode the response
     */
    public function decodeResponse(): array
    {
        return json_decode($this->response, true);
    }

    /**
     * Get the response
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Get the data from the response
     */
    public function getData(): array
    {
        return $this->getResponse()['data'];
    }
}
