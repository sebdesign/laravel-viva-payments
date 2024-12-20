<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/** @covers \Sebdesign\VivaPayments\Services\Order */
class OrderTest extends TestCase
{
    /**
     * @test
     *
     * @covers \Sebdesign\VivaPayments\Requests\CreatePaymentOrder
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_creates_a_payment_order(): void
    {
        $orderCode = Viva::orders()->create(new CreatePaymentOrder(
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
