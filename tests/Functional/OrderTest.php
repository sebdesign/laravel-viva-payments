<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Test\TestCase;

/** @cover \Sebdesign\VivaPayments\Order */
class OrderTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Requests\CreatePaymentOrder
     */
    public function it_creates_a_payment_order(): void
    {
        /** @var Order */
        $order = $this->app?->make(Order::class);

        $orderCode = $order->create(new CreatePaymentOrder(
            amount: 1000,
            customerTrns: 'Test customer description',
            customer: new Customer(
                email: 'johdoe@vivawallet.com',
                fullName: 'John Doe',
                phone: '+30999999999',
                countryCode: 'GB',
                requestLang: 'en-GB',
            ),
            sourceCode: strval(env('VIVA_SOURCE_CODE')),
            merchantTrns: 'Test merchant description',
        ));

        $this->assertIsNumeric($orderCode);
    }
}
