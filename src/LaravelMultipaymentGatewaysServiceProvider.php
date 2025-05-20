<?php

namespace Faysal0x1\LaraPayment;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Faysal0x1\LaraPayment\Contracts\FlutterwaveContract;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;
use Faysal0x1\LaraPayment\Contracts\SSLCommerzContract;
use Faysal0x1\LaraPayment\Contracts\StripeContract;
use Faysal0x1\LaraPayment\Exceptions\InvalidPaymentWebhookConfig;
use Faysal0x1\LaraPayment\Gateways\FlutterwaveService;
use Faysal0x1\LaraPayment\Gateways\PaystackService;
use Faysal0x1\LaraPayment\Gateways\SSLCommerzService;
use Faysal0x1\LaraPayment\Gateways\StripeService;
use Faysal0x1\LaraPayment\Http\Controllers\PaymentWebhookController;
use Faysal0x1\LaraPayment\Services\PaymentWebhookConfig;
use Faysal0x1\LaraPayment\Services\PaymentWebhookConfigRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMultipaymentGatewaysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('lara-payment')
            ->hasConfigFile()
            ->hasMigrations([
                'create_payment_webhook_logs_table',
                'create_multipayment_gateways_table',
                'create_orders_table'
            ]);
    }

    public function packageRegistered()
    {
        $this->app->bind(PaystackContract::class, PaystackService::class);
        $this->app->bind(StripeContract::class, StripeService::class);
        $this->app->bind(FlutterwaveContract::class, FlutterwaveService::class);
        $this->app->bind(SSLCommerzContract::class, SSLCommerzService::class);

        $this->registerWebHookConfig();
    }

    public function provides(): array
    {
        return [
            PaystackContract::class,
            StripeContract::class,
            PaymentWebhookConfigRepository::class,
            PaymentWebhookConfig::class,
            FlutterwaveContract::class,
            SSLCommerzContract::class,
        ];
    }

    private function registerWebHookConfig()
    {
        $this->registerWebHookRoute();
        $this->registerWebHookBindings();
    }

    private function registerWebHookRoute()
    {
        Route::macro('webhooks', function (string $url, string $name = 'stripe') {
            return Route::post($url, PaymentWebhookController::class)->name("{$name}-payment-webhook");
        });
    }

    private function registerWebHookBindings()
    {
        $this->app->scoped(PaymentWebhookConfigRepository::class, function () {
            $configRepository = new PaymentWebhookConfigRepository();
            $webhookConfigs = config('multipayment-gateways.webhooks');

            collect($webhookConfigs)
                ->map(fn (array $config) => new PaymentWebhookConfig($config))
                ->each(fn (PaymentWebhookConfig $webhookConfig) => $configRepository->storeConfig($webhookConfig));

            return $configRepository;
        });

        $this->app->bind(PaymentWebhookConfig::class, function () {
            $routeName = request()->route()?->getName() ?? 'stripe-payment-webhook';
            $configName = Str::before($routeName, '-payment-webhook');

            $paymentWebhookConfig = app(PaymentWebhookConfigRepository::class)->getConfig($configName);

            if (is_null($paymentWebhookConfig)) {
                throw InvalidPaymentWebhookConfig::webhookConfigMissing($configName);
            }

            return $paymentWebhookConfig;
        });
    }
}
