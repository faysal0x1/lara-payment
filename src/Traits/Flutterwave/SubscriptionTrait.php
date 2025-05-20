<?php

namespace Faysal0x1\LaravelMultipaymentGateways\Traits\Flutterwave;

use Faysal0x1\LaravelMultipaymentGateways\Constants\FlutterwaveConstant;

trait SubscriptionTrait
{
    /**
     * Get information for all subscriptions
     */
    public function getAllSubscriptions(array $queryParams = []): array
    {
        return $this->httpClient()->get(
            url: FlutterwaveConstant::SUBSCRIPTION_ENDPOINT,
            query: $queryParams
        );
    }

    /**
     * Activate a Subscription
     */
    public function activateSubscription(int $subscriptionId): array
    {
        return $this->httpClient()->put(
            url: FlutterwaveConstant::SUBSCRIPTION_ENDPOINT.$subscriptionId.'/activate',
        );
    }

    /**
     * Deactivate a Subscription
     */
    public function deactivateSubscription(int $subscriptionId): array
    {
        return $this->httpClient()->put(
            url: FlutterwaveConstant::SUBSCRIPTION_ENDPOINT.$subscriptionId.'/cancel',
        );
    }
}
