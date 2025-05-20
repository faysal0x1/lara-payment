<?php

declare(strict_types=1);

namespace Faysal0x1\LaraPayment\Facades;

use Illuminate\Support\Facades\Facade;
use Faysal0x1\LaraPayment\Contracts\SSLCommerzContract;

/**
 * @method static array initiatePayment(array $data)
 * @method static bool validateIPN(array $data)
 * 
 * @see \Faysal0x1\LaraPayment\Contracts\SSLCommerzContract
 */
class SSLCommerz extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SSLCommerzContract::class;
    }
} 