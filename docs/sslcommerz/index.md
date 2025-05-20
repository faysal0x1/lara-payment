# SSL Commerz Integration Guide

This comprehensive guide will help you integrate SSL Commerz payment gateway into your Laravel application using the Laravel Multipayment Gateways package.

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Basic Usage](#basic-usage)
- [Advanced Features](#advanced-features)
- [Webhook Handling](#webhook-handling)
- [Testing](#testing)
- [Security](#security)
- [Troubleshooting](#troubleshooting)

## Installation

1. Install the package via Composer:
```bash
composer require faysal0x1/lara-payment
```

2. Publish the configuration and assets:
```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="config"
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="controllers"
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="views"
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="migrations"
```

3. Run the migrations:
```bash
php artisan migrate
```

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
SSLCOMMERZ_SANDBOX=true
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ__STORE_PASSWORD=your_store_password
```

### Configuration File

The `config/sslcommerz.php` file contains these options:

```php
return [
    'sandbox' => env("SSLCOMMERZ_SANDBOX", false),
    'middleware' => 'web',
    'store_id' => env("SSLCOMMERZ_STORE_ID"),
    'store_password' => env("SSLCOMMERZ__STORE_PASSWORD"),
    'success_url' => '/sslcommerz/success',
    'failed_url' => '/sslcommerz/fail',
    'cancel_url' => '/sslcommerz/cancel',
    'ipn_url' => '/sslcommerz/ipn',
    'return_response' => 'html', // html or json
];
```

## Basic Usage

### Using Facade

```php
use Faysal0x1\LaraPayment\Facade\SSLCommerzPayment;

// Basic payment
$data = [
    'total_amount' => 100,
    'currency' => 'BDT',
    'tran_id' => uniqid(),
    'product_category' => 'Test Category'
];

$customer = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'address_1' => 'Dhaka',
    'phone' => '8801XXXXXXXXX',
    'country' => 'Bangladesh'
];

$shipment = [
    'shipping_method' => 'Yes',
    'ship_name' => 'John Doe',
    'ship_add1' => 'Dhaka',
    'ship_city' => 'Dhaka',
    'ship_country' => 'Bangladesh'
];

$response = SSLCommerzPayment::makePayment($data)
    ->setCustomerInfo($customer)
    ->setShipmentInfo($shipment);
```

### Using Controller

The package provides two example controllers:

1. Easy Checkout (`/example1`):
```php
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
```

2. Hosted Checkout (`/example2`):
```php
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);
```

### Available Routes

```php
Route::group(['middleware' => config('sslcommerz.middleware')], function () {
    Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
    Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);
    Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
    Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);
    Route::post('/success', [SslCommerzPaymentController::class, 'success']);
    Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
    Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);
    Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
});
```

## Advanced Features

### 1. EMI Payment Support

```php
$sslc = new SslCommerzNotification();
$sslc->enableEMI(3, 12, false); // 3 months installment, max 12 months
```

### 2. Airline Ticket Profile

```php
$airlineInfo = [
    'hours_till_departure' => '24',
    'flight_type' => 'domestic',
    'pnr' => 'ABC123',
    'journey_from_to' => 'Dhaka to Chittagong'
];
$sslc->setAirlineTicketProfile($airlineInfo);
```

### 3. Travel Vertical Profile

```php
$travelInfo = [
    'hotel_name' => 'Grand Hotel',
    'length_of_stay' => 3,
    'check_in_time' => '14:00',
    'hotel_city' => 'Dhaka'
];
$sslc->setTravelVerticalProfile($travelInfo);
```

### 4. Telecom Vertical Profile

```php
$telecomInfo = [
    'product_type' => 'prepaid',
    'topup_number' => '8801XXXXXXXXX',
    'country_topup' => 'Bangladesh'
];
$sslc->setTelecomVerticleProfile($telecomInfo);
```

## Webhook Handling

### IPN (Instant Payment Notification)

1. Add the webhook route:
```php
Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
```

2. Handle the IPN in your controller:
```php
public function ipn(Request $request)
{
    if ($request->input('tran_id')) {
        $tran_id = $request->input('tran_id');
        $order_details = $this->findOrder($tran_id);
        
        if ($order_details->status == 'Pending') {
            $validation = SSLCommerzPayment::orderValidate(
                $request->all(), 
                $tran_id, 
                $order_details->amount, 
                $order_details->currency
            );
            
            if ($validation) {
                $this->orderUpdate($tran_id, 'Processing');
                return SSLCommerzPayment::returnSuccess(
                    $tran_id,
                    "Transaction is successfully Completed",
                    '/'
                );
            }
        }
    }
    return SSLCommerzPayment::returnFail('', "Invalid Data", '/');
}
```

## Testing

### Sandbox Environment

1. Set sandbox mode in `.env`:
```env
SSLCOMMERZ_SANDBOX=true
```

2. Use test credentials:
- Store ID: `testbox`
- Store Password: `qwerty`

### Test Scenarios

1. Successful Payment:
- Use test card: 4111 1111 1111 1111
- Expiry: Any future date
- CVV: Any 3 digits

2. Failed Payment:
- Use test card: 4111 1111 1111 1111
- Expiry: Any past date
- CVV: Any 3 digits

## Security

### Best Practices

1. Environment Variables:
- Never commit `.env` file
- Use different credentials for development and production

2. SSL/TLS:
- Always use HTTPS in production
- Enable SSL verification

3. Data Validation:
- Validate all incoming data
- Use proper error handling
- Log all transactions

4. Store Credentials:
- Keep store ID and password secure
- Rotate credentials periodically
- Use strong passwords

## Troubleshooting

### Common Issues

1. Payment Not Processing:
- Check store credentials
- Verify sandbox mode setting
- Check network connectivity

2. IPN Not Working:
- Verify IPN URL is accessible
- Check server logs
- Validate IPN response

3. Transaction Validation Failed:
- Verify transaction amount
- Check currency
- Validate store credentials

### Support

For issues and questions:
1. Check the [GitHub repository](https://github.com/faysal0x1/lara-payment)
2. Create an issue with detailed information
3. Include logs and error messages
4. Provide steps to reproduce

## Database Structure

The package creates an `orders` table:

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('name', 191)->nullable();
    $table->string('email', 191)->nullable();
    $table->string('phone', 60)->nullable();
    $table->double('amount')->default(0);
    $table->text('address')->nullable();
    $table->string('status', 20)->default('Pending');
    $table->string('transaction_id', 191);
    $table->string('currency', 20)->nullable()->default('BDT');
    $table->timestamps();
});
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).