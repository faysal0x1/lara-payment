# Installation

By following these instructions, you will be able to easily install the package.

## Prerequisite
[PHP](https://php.net) 8.0, [Laravel](https://laravel.com) 8+ and [Composer](https://getcomposer.org) are required.

To get the latest version of the package, simply use composer

```bash
composer require faysal0x1/lara-payment
```

## Configuration

Publish the configuration file using this command:

```bash
php artisan vendor:publish --provider="Faysal0x1\LaraPayment\LaravelMultipaymentGatewaysServiceProvider"
```
