<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Services\ISV;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(ISV::class)]
#[CoversClass(ISV\Order::class)]
#[CoversClass(CreatePaymentOrder::class)]
class OrderTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_an_isv_payment_order(): void
    {
        Viva::withOAuthCredentials(
            strval(env('VIVA_ISV_CLIENT_ID')),
            strval(env('VIVA_ISV_CLIENT_SECRET')),
        );

        $orderCode = Viva::isv()->orders()->create(new CreatePaymentOrder(
            amount: 1000,
            customerTrns: 'Test customer description',
            customer: new Customer(
                email: 'test@vivawallet.com',
                fullName: 'John Doe',
                phone: '+30999999999',
                countryCode: 'GB',
                requestLang: 'en-GB',
            ),
            sourceCode: strval(env('VIVA_SOURCE_CODE')),
            merchantTrns: 'Test merchant description',
            isvAmount: 1,
            resellerSourceCode: 'Default',
        ));

        $this->assertIsNumeric($orderCode);
    }
}
