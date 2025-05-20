<?php

namespace Faysal0x1\LaravelMultipaymentGateways\Exceptions;

use Exception;

class PaymentVerificationException extends Exception
{
    protected $message = 'Payment verification failed, please check the payment reference and try again';
}
