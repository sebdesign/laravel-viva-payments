<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Fluent;
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
        /** @phpstan-ignore argument.type */
        $config = new Fluent(config('services.viva'));

        Viva::withOAuthCredentials(
            $config->string('isv_client_id'),
            $config->string('isv_client_secret'),
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
            sourceCode: $config->string('source_code'),
            merchantTrns: 'Test merchant description',
            isvAmount: 1,
            resellerSourceCode: 'Default',
        ));

        self::assertIsNumeric($orderCode);
    }
}
