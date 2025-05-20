<?php

namespace Faysal0x1\LaraPayment\Contracts;

use GuzzleHttp\Exception\GuzzleException;
use Faysal0x1\LaraPayment\Exceptions\HttpMethodFoundException;
use Faysal0x1\LaraPayment\Exceptions\InvalidConfigurationException;

interface StripeContract
{
    /**
     * Create a new payment intent
     *
     *
     * @throws GuzzleException
     * @throws HttpMethodFoundException
     * @throws InvalidConfigurationException
     */
    public function createIntent(array $data): array;

    /**
     * Confirm a payment intent
     *
     *
     * @throws GuzzleException
     * @throws HttpMethodFoundException
     * @throws InvalidConfigurationException
     */
    public function confirmIntent(string $paymentIntentId): array;
}
