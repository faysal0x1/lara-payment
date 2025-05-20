<?php

namespace Faysal0x1\LaravelMultipaymentGateways\SignatureValidator;

use Illuminate\Http\Request;
use Faysal0x1\LaravelMultipaymentGateways\Services\PaymentWebhookConfig;

interface PaymentWebhookSignatureValidator
{
    public function isValid(Request $request, PaymentWebhookConfig $config): bool;
}
