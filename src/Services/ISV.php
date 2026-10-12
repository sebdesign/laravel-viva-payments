<?php

namespace Sebdesign\VivaPayments\Services;

use Sebdesign\VivaPayments\Client;

class ISV
{
    public function __construct(protected Client $client) {}

    public function orders(): ISV\Order
    {
        return new ISV\Order($this->client);
    }

    public function sources(): ISV\Source
    {
        return new ISV\Source($this->client);
    }

    public function transactions(): ISV\Transaction
    {
        return new ISV\Transaction($this->client);
    }

    /**
     * Calculate the fee based on the given amount, fee percentage, and minimum fee.
     *
     * Uses bcmath for precision to avoid floating-point rounding errors.
     *
     * @param  int  $amount  The amount in cents (e.g., 1000 for €10.00)
     * @param  float  $percentage  The fee percentage (e.g., 0.05 for 0.05%)
     * @param  int  $minimum  The minimum fee in cents (e.g., 7 for €0.07)
     */
    public function fee(int $amount, float $percentage = 0.0, int $minimum = 0): int
    {
        // Format the percentage as a plain decimal string, since casting small
        // floats to strings uses scientific notation (e.g., 1.0E-5) which bcmath rejects
        $percentage = number_format($percentage, 10, '.', '');

        // Convert the percentage to a decimal (e.g., 0.05% → 0.0005)
        /** @phpstan-ignore missingType.checkedException */
        $feeDecimal = bcdiv($percentage, '100', scale: 10);

        // Calculate the fee in cents with sufficient precision (e.g., 0.0005 × 1000 = 0.5)
        $fee = bcmul($feeDecimal, (string) $amount, scale: 10);

        // Round half up to the nearest cent (e.g., 0.5 → 1, 0.4 → 0)
        $fee = (int) bcadd($fee, '0.5', scale: 0);

        return max($fee, $minimum);
    }
}
