# SSL Commerz Integration

This guide will help you integrate SSL Commerz payment gateway into your Laravel application using the Laravel Multipayment Gateways package.

## Installation

First, make sure you have installed the package and published its configuration:

```bash
composer require faysal0x1/laravel-multipayment-gateways
php artisan vendor:publish --provider="Faysal0x1\LaravelMultipaymentGateways\LaravelMultipaymentGatewaysServiceProvider"
```

## Configuration

Add the following environment variables to your `.env` file:

```env
SSLCOMMERZ_BASE_URI=https://sandbox.sslcommerz.com
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_password
SSLCOMMERZ_SUCCESS_URL=your_success_url
SSLCOMMERZ_FAIL_URL=your_fail_url
SSLCOMMERZ_CANCEL_URL=your_cancel_url
SSLCOMMERZ_IPN_URL=your_ipn_url
SSLCOMMERZ_CURRENCY=BDT
```

## Usage

### 1. Initialize Payment

To initiate a payment, inject the `SSLCommerzContract` into your controller:

```php
use Faysal0x1\LaravelMultipaymentGateways\Contracts\SSLCommerzContract;

class PaymentController extends Controller
{
    public function __construct(private SSLCommerzContract $sslCommerz)
    {
    }

    public function initiatePayment(Request $request)
    {
        $data = [
            'total_amount' => 100,
            'tran_id' => 'ORDER_' . uniqid(),
            'product_name' => 'Test Product',
            'product_category' => 'Test Category',
            'product_profile' => 'general',
            'cus_name' => 'Customer Name',
            'cus_email' => 'customer@example.com',
            'cus_add1' => 'Customer Address',
            'cus_phone' => 'Customer Phone',
        ];

        $response = $this->sslCommerz->initiatePayment($data);
        
        // Redirect to SSL Commerz payment page
        return redirect($response['GatewayPageURL']);
    }
}
```

### 2. Handle Payment Response

Create routes to handle success, fail, and cancel URLs:

```php
// routes/web.php
Route::get('payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::get('payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
```

Handle the responses in your controller:

```php
public function success(Request $request)
{
    if ($this->sslCommerz->validateIPN($request->all())) {
        // Payment is successful
        // Process your order
        return view('payment.success');
    }
    
    return redirect()->route('payment.fail');
}

public function fail(Request $request)
{
    // Handle failed payment
    return view('payment.fail');
}

public function cancel(Request $request)
{
    // Handle cancelled payment
    return view('payment.cancel');
}
```

### 3. Handle IPN (Instant Payment Notification)

Add the webhook route to handle IPN:

```php
// routes/web.php
Route::webhooks('payment/sslcommerz/webhook', 'sslcommerz');
```

Create a job to handle the webhook:

```php
namespace App\Jobs;

use Faysal0x1\LaravelMultipaymentGateways\Jobs\ProcessPaymentWebhookJob;

class ProcessSSLCommerzWebhookJob extends ProcessPaymentWebhookJob
{
    public function handle()
    {
        $payload = $this->webhookCall->payload;
        
        if ($this->sslCommerz->validateIPN($payload)) {
            // Payment is valid
            // Update your order status
            // Send notification
        }
    }
}
```

Register the job in your config:

```php
// config/multipayment-gateways.php
'webhooks' => [
    [
        'name' => 'sslcommerz',
        'payment_webhook_job' => App\Jobs\ProcessSSLCommerzWebhookJob::class,
        // ... other config
    ],
],
```

## Available Parameters

When initiating a payment, you can pass the following parameters:

| Parameter | Required | Description |
|-----------|----------|-------------|
| total_amount | Yes | Total amount to be paid |
| tran_id | Yes | Unique transaction ID |
| product_name | Yes | Name of the product |
| product_category | Yes | Category of the product |
| product_profile | Yes | Profile of the product (general, physical-goods, etc.) |
| cus_name | Yes | Customer name |
| cus_email | Yes | Customer email |
| cus_add1 | No | Customer address line 1 |
| cus_add2 | No | Customer address line 2 |
| cus_city | No | Customer city |
| cus_state | No | Customer state |
| cus_postcode | No | Customer postal code |
| cus_country | No | Customer country (default: Bangladesh) |
| cus_phone | No | Customer phone number |
| cus_fax | No | Customer fax number |
| shipping_method | No | Shipping method (default: NO) |
| ship_name | No | Shipping name |
| ship_add1 | No | Shipping address line 1 |
| ship_add2 | No | Shipping address line 2 |
| ship_city | No | Shipping city |
| ship_state | No | Shipping state |
| ship_postcode | No | Shipping postal code |
| ship_country | No | Shipping country (default: Bangladesh) |

## Testing

For testing, use the sandbox environment by setting:

```env
SSLCOMMERZ_BASE_URI=https://sandbox.sslcommerz.com
```

Use these test credentials:
- Store ID: `testbox`
- Store Password: `qwerty`

## Error Handling

The package provides a `PaymentVerificationException` for handling payment verification failures:

```php
use Faysal0x1\LaravelMultipaymentGateways\Exceptions\PaymentVerificationException;

try {
    $response = $this->sslCommerz->initiatePayment($data);
} catch (PaymentVerificationException $e) {
    // Handle payment verification failure
    return back()->with('error', $e->getMessage());
}
```

## Security Considerations

1. Always validate IPN responses using the `validateIPN` method
2. Keep your store ID and password secure
3. Use HTTPS for all payment-related URLs
4. Implement proper error handling
5. Log all payment transactions for audit purposes 