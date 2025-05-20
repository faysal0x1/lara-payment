<?php

// config for Faysal0x1/LaraPayment
return [
    'paystack' => [
        'base_uri' => env('PAYSTACK_BASE_URI'),
        'secret' => env('PAYSTACK_SECRET'),
        'currency' => env('PAYSTACK_CURRENCY'),
    ],

    'stripe' => [
        'base_uri' => env('STRIPE_BASE_URI'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY'),
        'plans' => [
            'monthly' => env('STRIPE_MONTHLY_PLAN'),
            'yearly' => env('STRIPE_YEARLY_PLAN'),
        ],
    ],

    'flutterwave' => [
        'base_uri' => env('FLUTTERWAVE_BASE_URI'),
        'secret' => env('FLUTTERWAVE_SECRET'),
        'currency' => env('FLUTTERWAVE_CURRENCY'),
        'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
    ],

    'sslcommerz' => [
        'base_uri' => env('SSLCOMMERZ_BASE_URI', 'https://sandbox.sslcommerz.com'),
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'success_url' => env('SSLCOMMERZ_SUCCESS_URL'),
        'fail_url' => env('SSLCOMMERZ_FAIL_URL'),
        'cancel_url' => env('SSLCOMMERZ_CANCEL_URL'),
        'ipn_url' => env('SSLCOMMERZ_IPN_URL'),
        'currency' => env('SSLCOMMERZ_CURRENCY', 'BDT'),
    ],

    'webhooks' => [
        [
            /*
             * This refers to the name of the payment gateway being used.
             */
            'name' => 'stripe',

            /**
             * When set to false, the package will not verify the signature of the webhook call.
             */
            'verify_signature' => true,

            /*
             * This secret key is used to validate the signature of the webhook call.
             */
            'signing_secret' => '',

            /*
             * This refers to the header that holds the signature.
             */
            'signature_header_name' => 'Stripe-Signature',

            /*
             *  This class is responsible for verifying the validity of the signature header.
             *
             * It should implement the interface \Faysal0x1\LaraPayment\SignatureValidator\PaymentWebhookSignatureValidator.
             */
            'signature_validator' => \Faysal0x1\LaraPayment\SignatureValidator\DefaultSignatureValidator::class,

            /**
             * The webhook handler option allows you to choose how webhook requests are handled in your application.
             *
             * Available options:
             * - 'job': Webhook requests will be handled by a job.
             * - 'event': Webhook requests will be handled by an event.
             *
             * Default: 'job'
             */
            'payment_webhook_handler' => 'job',

            /**
             * The payment_webhook_job option allows you to specify the job class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Jobs\ProcessPaymentWebhookJob.
             */
            'payment_webhook_job' => '',

            /**
             * The payment_webhook_event option allows you to specify the event class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Events\PaymentWebhookReceivedEvent.
             */
            'payment_webhook_event' => '',
        ],

        [
            /*
             * This refers to the name of the payment gateway being used.
             */
            'name' => 'paystack',

            /**
             * When set to false, the package will not verify the signature of the webhook call.
             */
            'verify_signature' => true,

            /*
             * This secret key is used to validate the signature of the webhook call.
             */
            'signing_secret' => env('PAYSTACK_SECRET'),

            /*
             * This refers to the header that holds the signature.
             */
            'signature_header_name' => 'HTTP_X_PAYSTACK_SIGNATURE',

            /*
             *  This class is responsible for verifying the validity of the signature header.
             *
            * It should implement the interface \Faysal0x1\LaraPayment\SignatureValidator\PaymentWebhookSignatureValidator.
             */
            'signature_validator' => \Faysal0x1\LaraPayment\SignatureValidator\DefaultSignatureValidator::class,

            /**
             * The webhook handler option allows you to choose how webhook requests are handled in your application.
             *
             * Available options:
             * - 'job': Webhook requests will be handled by a job.
             * - 'event': Webhook requests will be handled by an event.
             *
             * Default: 'job'
             */
            'payment_webhook_handler' => 'job',

            /**
             * The payment_webhook_job option allows you to specify the job class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Jobs\ProcessPaymentWebhookJob.
             */
            'payment_webhook_job' => '',

            /**
             * The payment_webhook_event option allows you to specify the event class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Events\PaymentWebhookReceivedEvent.
             */
            'payment_webhook_event' => '',
        ],

        [
            /*
             * This refers to the name of the payment gateway being used.
             */
            'name' => 'flutterwave',

            /**
             * When set to false, the package will not verify the signature of the webhook call.
             */
            'verify_signature' => true,

            /*
             * This secret key is used to validate the signature of the webhook call.
             */
            'signing_secret' => env('FLUTTERWAVE_SECRET_HASH'),

            /*
             * This refers to the header that holds the signature.
             */
            'signature_header_name' => 'verif-hash',

            /*
             *  This class is responsible for verifying the validity of the signature header.
             *
            * It should implement the interface \Faysal0x1\LaraPayment\SignatureValidator\PaymentWebhookSignatureValidator.
             */
            'signature_validator' => \Faysal0x1\LaraPayment\SignatureValidator\DefaultSignatureValidator::class,

            /**
             * The webhook handler option allows you to choose how webhook requests are handled in your application.
             *
             * Available options:
             * - 'job': Webhook requests will be handled by a job.
             * - 'event': Webhook requests will be handled by an event.
             *
             * Default: 'job'
             */
            'payment_webhook_handler' => 'job',

            /**
             * The payment_webhook_job option allows you to specify the job class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Jobs\ProcessPaymentWebhookJob.
             */
            'payment_webhook_job' => '',

            /**
             * The payment_webhook_event option allows you to specify the event class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Events\PaymentWebhookReceivedEvent.
             */
            'payment_webhook_event' => '',
        ],

        [
            /*
             * This refers to the name of the payment gateway being used.
             */
            'name' => 'sslcommerz',

            /**
             * When set to false, the package will not verify the signature of the webhook call.
             */
            'verify_signature' => true,

            /*
             * This secret key is used to validate the signature of the webhook call.
             */
            'signing_secret' => env('SSLCOMMERZ_STORE_PASSWORD'),

            /*
             * This refers to the header that holds the signature.
             */
            'signature_header_name' => 'HTTP_X_SSLCOMMERZ_SIGNATURE',

            /*
             *  This class is responsible for verifying the validity of the signature header.
             *
             * It should implement the interface \Faysal0x1\LaraPayment\SignatureValidator\PaymentWebhookSignatureValidator.
             */
            'signature_validator' => \Faysal0x1\LaraPayment\SignatureValidator\DefaultSignatureValidator::class,

            /**
             * The webhook handler option allows you to choose how webhook requests are handled in your application.
             *
             * Available options:
             * - 'job': Webhook requests will be handled by a job.
             * - 'event': Webhook requests will be handled by an event.
             *
             * Default: 'job'
             */
            'payment_webhook_handler' => 'job',

            /**
             * The payment_webhook_job option allows you to specify the job class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Jobs\ProcessPaymentWebhookJob.
             */
            'payment_webhook_job' => '',

            /**
             * The payment_webhook_event option allows you to specify the event class that will be used to process webhook requests for payment methods.
             *
             * This should be set to a class that extends \Faysal0x1\LaraPayment\Events\PaymentWebhookReceivedEvent.
             */
            'payment_webhook_event' => '',
        ],
    ],
];
