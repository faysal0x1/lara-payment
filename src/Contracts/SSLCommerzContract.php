<?php

declare(strict_types=1);

namespace Faysal0x1\LaraPayment\Contracts;

interface SSLCommerzContract
{
    /**
     * Initialize a payment session
     */
    public function initiatePayment(array $data): array;

    /**
     * Validate IPN response
     */
    public function validateIPN(array $data): bool;
} 