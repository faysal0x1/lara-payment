# Bank API

## Get Banks

Get list of all banks.

```php
use Faysal0x1\LaraPayment\Facades\Paystack;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;

// Using Facade
$banks = Paystack::getBanks();

// Using Dependency Injection
public function __invoke(PaystackContract $paystack)
{
    $banks = $paystack->getBanks();
}

// Using Helper
$banks = paystack()->getBanks();

```

## Resolve Account Number

Resolve account a bank account number.

```php
use Faysal0x1\LaraPayment\Facades\Paystack;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;

$payload = [
    'account_number' => 1234567890,
    'bank_code' => 044,
];

// Using Facade
$account = Paystack::resolveAccountNumber($payload);

// Using Dependency Injection
public function __invoke(PaystackContract $paystack)
{
    $account = $paystack->resolveAccountNumber($payload);
}

// Using Helper
$account = paystack()->resolveAccountNumber($payload);

```
