<?php

namespace Faysal0x1\LaraPayment\Facades;

use Illuminate\Support\Facades\Facade;
use Faysal0x1\LaraPayment\Contracts\StripeContract;
use Faysal0x1\LaraPayment\Services\HttpClientWrapper;

/**
 * @method static HttpClientWrapper httpClient()
 * @method static HttpClientWrapper get(string $url, array $query = [], array $headers = [])
 * @method static HttpClientWrapper post(string $url, array $formParams = [], array $query = [], array $headers = [])
 * @method static HttpClientWrapper put(string $url, array $formParams = [], array $query = [], array $headers = [])
 * @method static HttpClientWrapper delete(string $url, array $formParams = [], array $query = [], array $headers = [])
 * @method static HttpClientWrapper patch(string $url, array $formParams = [], array $query = [], array $headers = [])
 *
 * @see \Faysal0x1\LaraPayment\Gateways\StripeService
 */
class Stripe extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StripeContract::class;
    }
}
