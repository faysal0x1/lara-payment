<?php

namespace Faysal0x1\LaraPayment\Http\Controllers;

use Illuminate\Http\Request;
use Faysal0x1\LaraPayment\Services\PaymentWebhookConfig;
use Faysal0x1\LaraPayment\Services\PaymentWebhookHandler;

class PaymentWebhookController
{
    public function __invoke(Request $request, PaymentWebhookConfig $config)
    {
        return (new PaymentWebhookHandler($request, $config))->handle();
    }
}
