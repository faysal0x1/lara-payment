<?php

namespace Faysal0x1\LaravelMultipaymentGateways\Services;

use Faysal0x1\LaravelMultipaymentGateways\Events\PaymentWebhookReceivedEvent;
use Faysal0x1\LaravelMultipaymentGateways\Exceptions\InvalidPaymentWebhookConfig;
use Faysal0x1\LaravelMultipaymentGateways\Jobs\ProcessPaymentWebhookJob;
use Faysal0x1\LaravelMultipaymentGateways\Models\PaymentWebhookLog;
use Faysal0x1\LaravelMultipaymentGateways\SignatureValidator\PaymentWebhookSignatureValidator;

class PaymentWebhookConfig
{
    public string $name;

    public string $signingSecret;

    public string $signatureHeaderName;

    public PaymentWebhookSignatureValidator $signatureValidator;

    public string $paymentWebhookModel;

    public string $paymentWebhookHandler;

    public string $verify_signature;

    public string|ProcessPaymentWebhookJob $paymentWebhookJobClass;

    public string|PaymentWebhookReceivedEvent $paymentWebhookEventClass;

    /**
     * @throws InvalidPaymentWebhookConfig
     */
    public function __construct(array $properties)
    {
        $this->name = $properties['name'];

        $this->verify_signature = $properties['verify_signature'];

        $this->signingSecret = $properties['signing_secret'] ?? '';

        $this->signatureHeaderName = $properties['signature_header_name'] ?? '';

        $this->paymentWebhookModel = PaymentWebhookLog::class;

        $this->paymentWebhookHandler = $properties['payment_webhook_handler'];

        if (! empty($properties['signature_validator']) && ! is_subclass_of($properties['signature_validator'], PaymentWebhookSignatureValidator::class)) {
            throw InvalidPaymentWebhookConfig::invalidSignatureValidator($properties['signature_validator']);
        }

        $this->signatureValidator = app($properties['signature_validator']);

        if (! empty($properties['payment_webhook_job']) && ! is_subclass_of($properties['payment_webhook_job'], ProcessPaymentWebhookJob::class)) {
            throw InvalidPaymentWebhookConfig::invalidWebhookJob($properties['payment_webhook_job']);
        }

        $this->paymentWebhookJobClass = $properties['payment_webhook_job'];

        if (! empty($properties['payment_webhook_event']) && ! is_subclass_of($properties['payment_webhook_event'], PaymentWebhookReceivedEvent::class)) {
            throw InvalidPaymentWebhookConfig::invalidWebhookEvent($properties['payment_webhook_event']);
        }

        $this->paymentWebhookEventClass = $properties['payment_webhook_event'];
    }
}
