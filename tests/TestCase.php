<?php

namespace Faysal0x1\LaraPayment\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faysal0x1\LaraPayment\Contracts\FlutterwaveContract;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;
use Faysal0x1\LaraPayment\Contracts\StripeContract;
use Faysal0x1\LaraPayment\LaravelMultipaymentGatewaysServiceProvider;
use Faysal0x1\LaraPayment\Services\HttpClientWrapper;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Faysal0x1\\LaraPayment\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->paystack = $this->instance('paystack', $this->mock(PaystackContract::class));
        $this->flutterwave = $this->instance('flutterwave', $this->mock(FlutterwaveContract::class));
        $this->stripe = $this->instance('stripe', $this->mock(StripeContract::class));

        $this->httpClientWrapper = $this->instance('httpClientWrapper', $this->mock(HttpClientWrapper::class));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMultipaymentGatewaysServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_lara-payment_table.php.stub';
        $migration->up();
        */
    }
}
