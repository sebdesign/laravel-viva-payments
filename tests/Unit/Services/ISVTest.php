<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Services\ISV;
use Sebdesign\VivaPayments\Test\TestCase;

#[CoversClass(ISV::class)]
class ISVTest extends TestCase
{
    private ISV $isv;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the HTTP mock client. Even though fee() doesn't make
        // HTTP requests, TestCase::$client must be initialized first.
        $this->mockJsonResponses([]);
        $this->mockRequests();

        $this->isv = new ISV($this->client);
    }

    #[Test]
    public function it_calculates_a_fee_with_rounding_up(): void
    {
        // 0.05% of €10.00 (1000¢) = 0.5¢ → rounds up to 1¢
        $fee = $this->isv->fee(amount: 1000, percentage: 0.05);

        self::assertSame(1, $fee);
    }

    #[Test]
    public function it_applies_the_minimum_fee(): void
    {
        // 0.05% of €10.00 = 1¢, but minimum is €0.07 (7¢)
        $fee = $this->isv->fee(amount: 1000, percentage: 0.05, minimum: 7);

        self::assertSame(7, $fee);
    }

    #[Test]
    public function it_returns_zero_when_percentage_is_zero(): void
    {
        $fee = $this->isv->fee(amount: 1000, percentage: 0.0);

        self::assertSame(0, $fee);
    }

    #[Test]
    public function it_returns_zero_when_amount_is_zero(): void
    {
        $fee = $this->isv->fee(amount: 0, percentage: 0.05);

        self::assertSame(0, $fee);
    }

    #[Test]
    public function it_calculates_a_fee_with_rounding_down(): void
    {
        // 0.04% of €10.00 (1000¢) = 0.4¢ → rounds down to 0¢
        $fee = $this->isv->fee(amount: 1000, percentage: 0.04);

        self::assertSame(0, $fee);
    }

    #[Test]
    public function it_calculates_a_one_percent_fee(): void
    {
        // 1.0% of €100.00 (10000¢) = 100¢ = €1.00
        $fee = $this->isv->fee(amount: 10000, percentage: 1.0);

        self::assertSame(100, $fee);
    }

    #[Test]
    public function it_returns_the_minimum_when_fee_is_lower(): void
    {
        // 0% of €5.00 = 0¢, but minimum is €0.50 (50¢)
        $fee = $this->isv->fee(amount: 500, percentage: 0.0, minimum: 50);

        self::assertSame(50, $fee);
    }

    #[Test]
    public function it_calculates_a_fee_for_larger_amounts(): void
    {
        // 0.05% of €1000.00 (100000¢) = 50¢ = €0.50
        $fee = $this->isv->fee(amount: 100000, percentage: 0.05);

        self::assertSame(50, $fee);
    }

    #[Test]
    public function it_preserves_precision_for_sub_two_decimal_percentages(): void
    {
        // 0.055% of €10.00 (1000¢) = 0.55¢ → rounds up to 1¢
        // Test that percentages with >2 decimal places aren't silently truncated
        $fee = $this->isv->fee(amount: 1000, percentage: 0.055);

        self::assertSame(1, $fee);
    }

    #[Test]
    public function it_handles_a_one_hundred_percent_fee(): void
    {
        // 100% of €1.00 (100¢) = 100¢
        $fee = $this->isv->fee(amount: 100, percentage: 100.0);

        self::assertSame(100, $fee);
    }

    #[Test]
    public function it_handles_the_midpoint_correctly(): void
    {
        // 0.025% of €20.00 (2000¢) = 0.5¢ → rounds up to 1¢
        $fee = $this->isv->fee(amount: 2000, percentage: 0.025);

        self::assertSame(1, $fee);
    }

    #[Test]
    public function it_returns_zero_when_amount_and_percentage_are_zero(): void
    {
        $fee = $this->isv->fee(amount: 0, percentage: 0.0);

        self::assertSame(0, $fee);
    }

    #[Test]
    public function it_handles_percentages_cast_to_scientific_notation(): void
    {
        // 0.00001% of €1,000,000.00 (100000000¢) = 10¢
        $fee = $this->isv->fee(amount: 100000000, percentage: 0.00001);

        self::assertSame(10, $fee);
    }
}
