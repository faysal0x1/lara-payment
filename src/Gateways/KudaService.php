<?php

namespace Faysal0x1\LaravelMultipaymentGateways\Gateways;

use Faysal0x1\LaravelMultipaymentGateways\Abstracts\BaseGateWay;
use Faysal0x1\LaravelMultipaymentGateways\Exceptions\InvalidConfigurationException;

class KudaService extends BaseGateWay
{
    public ?string $email;

    public function setPaymentGateway(): void
    {
        $this->paymentGateway = 'kuda';
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function setBaseUri(): void
    {
        $baseUri = config('multipaymentgateways.kuda.base_uri');

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
        $secret = config('multipaymentgateways.kuda.secret');

        if (! $secret) {
            throw new InvalidConfigurationException("The Secret for `{$this->paymentGateway}` is missing. Please ensure that the `secret` config key for `{$this->paymentGateway}` is set correctly.");
        }

        $this->secret = $secret;
        $this->email = config('multipaymentgateways.kuda.email');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers): void
    {
        // extend the base class method to add the authorization header
        parent::resolveAuthorization($queryParams, $formParams, $headers);

        // add the authorization header
        $headers['apiKey'] = $this->secret;
        $headers['email'] = $this->email;
    }

    public function resolveAccessToken(): string
    {
        return "Bearer {$this->retrieveApiToken()}";
    }

    public function retrieveApiToken()
    {
        return cache()->remember('kuda_token', now()->addMinutes(20), function () {
            return $this->makeRequest(
                'POST',
                $this->baseUri.'/account/gettoken',
                [
                    'email' => $this->email,
                    'apiKey' => $this->secret,
                ],
                true
            );
        });
    }

    /**
     * Decode the response
     */
    public function decodeResponse(): array
    {
        return json_decode($this->response, true);
    }
}
