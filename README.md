# A Laravel Package that makes implementation of multiple payment Gateways endpoints and webhooks seamless

[![Latest Version on Packagist](https://img.shields.io/packagist/v/faysal0x1/lara-payment.svg?style=flat-square)](https://packagist.org/packages/faysal0x1/lara-payment)
<a href="https://packagist.org/packages/faysal0x1/lara-payment"><img src="https://img.shields.io/packagist/php-v/faysal0x1/lara-payment.svg?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://packagist.org/packages/faysal0x1/lara-payment"><img src="https://img.shields.io/badge/Laravel-8.x,%209.x,%2010.x,%2011.x-brightgreen.svg?style=flat-square" alt="Laravel Version"></a>
![Test Status](https://img.shields.io/github/actions/workflow/status/faysal0x1/lara-payment/run-tests.yml?branch=main&label=Tests)
![Code Style Status](https://img.shields.io/github/actions/workflow/status/faysal0x1/lara-payment/phpstan.yml?branch=main&label=Code%20Style)
[![Total Downloads](https://img.shields.io/packagist/dt/faysal0x1/lara-payment.svg?style=flat-square)](https://packagist.org/packages/faysal0x1/lara-payment)

The `lara-payment` package provides a convenient way to handle payments through multiple payment gateways in a **Laravel 8, 9 and 10 application**.
The package currently supports multiple gateways such as **Paystack**, **Flutterwave** and **Stripe**.
The package offers an easy to use interface that abstracts the complexities of integrating with these payment gateways.
It also provides a way to handle webhooks from the payment gateways.

## Documentation
A comprehensive documentation is available to help you get started with the package [here](https://lara-payment-xi.vercel.app)


## Testing

```bash
php artisan test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Faysal0x1](https://github.com/Faysal0x1)
- [Cybernerdie](https://github.com/cybernerdie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# Laravel SSLCommerz Payment Gateway Integration

A Laravel package for integrating SSLCommerz payment gateway into your Laravel application. This package provides a simple and flexible way to handle payments through SSLCommerz in your Laravel projects.

## Features

- Easy checkout and hosted checkout options
- AJAX payment support
- IPN (Instant Payment Notification) handling
- Success, fail, and cancel URL handling
- Order validation
- Sandbox mode support
- Multiple payment methods support
- EMI payment support
- Customizable payment forms
- Comprehensive error handling

## Requirements

- PHP >= 7.4
- Laravel >= 8.0
- SSLCommerz merchant account

## Installation

1. Install the package via Composer:

```bash
composer require faysal0x1/lara-payment
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="config"
```

3. Publish the controller:

```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="controllers"
```

4. Publish the views:

```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="views"
```

5. Publish the migrations:

```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\SslcommerzLaravelServiceProvider" --tag="migrations"
```

6. Run the migrations:

```bash
php artisan migrate
```

## Configuration

Add the following environment variables to your `.env` file:

```env
SSLCOMMERZ_SANDBOX=true
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ__STORE_PASSWORD=your_store_password
```

The configuration file (`config/sslcommerz.php`) contains the following options:

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

## Usage

### Basic Payment Integration

1. Using the Facade:

```php
use Faysal0x1\LaraPayment\Facade\SSLCommerzPayment;

// Make payment
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

2. Using the Controller:

Visit `/example1` for AJAX payment or `/example2` for hosted payment.

### Available Routes

```php
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);
Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);
Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);
Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
```

### Advanced Features

1. EMI Payment Support:

```php
$sslc = new SslCommerzNotification();
$sslc->enableEMI(3, 12, false); // 3 months installment, max 12 months, not restricted to EMI only
```

2. Airline Ticket Profile:

```php
$airlineInfo = [
    'hours_till_departure' => '24',
    'flight_type' => 'domestic',
    'pnr' => 'ABC123',
    'journey_from_to' => 'Dhaka to Chittagong'
];
$sslc->setAirlineTicketProfile($airlineInfo);
```

3. Travel Vertical Profile:

```php
$travelInfo = [
    'hotel_name' => 'Grand Hotel',
    'length_of_stay' => 3,
    'check_in_time' => '14:00',
    'hotel_city' => 'Dhaka'
];
$sslc->setTravelVerticalProfile($travelInfo);
```

4. Telecom Vertical Profile:

```php
$telecomInfo = [
    'product_type' => 'prepaid',
    'topup_number' => '8801XXXXXXXXX',
    'country_topup' => 'Bangladesh'
];
$sslc->setTelecomVerticleProfile($telecomInfo);
```

### Response Handling

1. Success Response:

```php
return SSLCommerzPayment::returnSuccess($transId, "Transaction is successfully Completed", '/');
```

2. Failure Response:

```php
return SSLCommerzPayment::returnFail($transId, "Transaction is Failed", '/');
```

## Database Structure

The package creates an `orders` table with the following structure:

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

## Security Considerations

1. Always use environment variables for sensitive data
2. Enable SSL on your production environment
3. Validate all incoming data
4. Use proper error handling
5. Keep your store credentials secure

## Testing

1. Set `SSLCOMMERZ_SANDBOX=true` in your `.env` file for testing
2. Use test credentials provided by SSLCommerz
3. Test all payment scenarios (success, fail, cancel)
4. Verify IPN handling
5. Test with different currencies and amounts

## Support

For any issues or questions, please create an issue in the GitHub repository.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
