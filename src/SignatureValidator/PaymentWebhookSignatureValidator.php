<?php

namespace Faysal0x1\LaraPayment\SignatureValidator;

use Illuminate\Http\Request;
use Faysal0x1\LaraPayment\Services\PaymentWebhookConfig;

interface PaymentWebhookSignatureValidator
{
    public function isValid(Request $request, PaymentWebhookConfig $config): bool;
}
