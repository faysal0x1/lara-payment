<?php

namespace Faysal0x1\LaravelMultipaymentGateways\Http\Controllers;

use Illuminate\Http\Request;
use Faysal0x1\LaravelMultipaymentGateways\Services\PaymentWebhookConfig;
use Faysal0x1\LaravelMultipaymentGateways\Services\PaymentWebhookHandler;

class PaymentWebhookController
{
    public function __invoke(Request $request, PaymentWebhookConfig $config)
    {
        return (new PaymentWebhookHandler($request, $config))->handle();
    }
}
