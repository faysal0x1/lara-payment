<?php

use Faysal0x1\LaraPayment\Contracts\FlutterwaveContract;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;
use Faysal0x1\LaraPayment\Contracts\StripeContract;

if (! function_exists('paystack')) {
    function paystack(): PaystackContract
    {
        return app()->make(PaystackContract::class);
    }
}

if (! function_exists('stripe')) {
    function stripe(): StripeContract
    {
        return app(StripeContract::class);
    }
}

if (! function_exists('flutterwave')) {
    function flutterwave(): FlutterwaveContract
    {
        return app(FlutterwaveContract::class);
    }
}
