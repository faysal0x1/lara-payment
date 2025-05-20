<?php

namespace Faysal0x1\LaraPayment\Exceptions;

use Exception;

class HttpClientException extends Exception
{
    protected $message = 'An error occurred while sending the request to the payment gateway';
}
