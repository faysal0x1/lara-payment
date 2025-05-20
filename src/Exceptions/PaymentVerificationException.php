<?php

namespace Faysal0x1\LaraPayment\Exceptions;

use Exception;
use Faysal0x1\LaraPayment\Contracts\SSLCommerzContract;

class PaymentVerificationException extends Exception
{
    protected $message = 'Payment verification failed, please check the payment reference and try again';

    public function __construct(private SSLCommerzContract $sslCommerz)
    {
        parent::__construct($this->message);
    }
}
