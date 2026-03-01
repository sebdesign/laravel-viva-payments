<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Services\ISV;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(ISV::class)]
#[CoversClass(ISV\Order::class)]
#[CoversClass(CreatePaymentOrder::class)]
#[CoversClass(Customer::class)]
class OrderTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_an_isv_payment_order(): void
    {
        $this->mockJsonResponses(['orderCode' => '1272214778972604']);
        $this->mockRequests();

        $order = $this->client->withToken('test', Carbon::now()->addHour())->isv()->orders();

        $orderCode = $order->create(new CreatePaymentOrder(
            amount: 1000,
            customerTrns: 'Short description of purchased items/services to display to your customer',
            customer: new Customer(
                email: 'johdoe@vivawallet.com',
                fullName: 'John Doe',
                phone: '+30999999999',
                countryCode: 'GB',
                requestLang: 'en-GB',
            ),
            paymentTimeOut: 300,
            currencyCode: '978',
            preauth: false,
            allowRecurring: false,
            maxInstallments: 12,
            paymentNotification: true,
            tipAmount: 100,
            disableExactAmount: false,
            disableCash: true,
            disableWallet: true,
            sourceCode: '1234',
            merchantTrns: 'Short description of items/services purchased by customer',
            tags: [
                'tags for grouping and filtering the transactions',
                'this tag can be searched on VivaWallet sales dashboard',
                'Sample tag 1',
                'Sample tag 2',
                'Another string',
            ],
            isvAmount: 1,
            resellerSourceCode: 'Default',
        ));

        $request = $this->getLastRequest();

        self::assertMethod('POST', $request);
        self::assertQuery('merchantId', strval(config('services.viva.merchant_id')), $request);
        self::assertJsonBody('amount', 1000, $request);
        self::assertJsonBody('customerTrns', 'Short description of purchased items/services to display to your customer', $request);
        self::assertJsonBody('customer', [
            'email' => 'johdoe@vivawallet.com',
            'fullName' => 'John Doe',
            'phone' => '+30999999999',
            'countryCode' => 'GB',
            'requestLang' => 'en-GB',
        ], $request);
        self::assertJsonBody('paymentTimeOut', 300, $request);
        self::assertJsonBody('currencyCode', '978', $request);
        self::assertJsonBody('preauth', false, $request);
        self::assertJsonBody('allowRecurring', false, $request);
        self::assertJsonBody('maxInstallments', 12, $request);
        self::assertJsonBody('paymentNotification', true, $request);
        self::assertJsonBody('tipAmount', 100, $request);
        self::assertJsonBody('disableExactAmount', false, $request);
        self::assertJsonBody('disableCash', true, $request);
        self::assertJsonBody('disableWallet', true, $request);
        self::assertJsonBody('sourceCode', '1234', $request);
        self::assertJsonBody('merchantTrns', 'Short description of items/services purchased by customer', $request);
        self::assertJsonBody('tags', [
            'tags for grouping and filtering the transactions',
            'this tag can be searched on VivaWallet sales dashboard',
            'Sample tag 1',
            'Sample tag 2',
            'Another string',
        ], $request);
        self::assertJsonBody('isvAmount', 1, $request);
        self::assertJsonBody('resellerSourceCode', 'Default', $request);
        self::assertSame('1272214778972604', $orderCode, 'The order code should be 1272214778972604');
    }
}
