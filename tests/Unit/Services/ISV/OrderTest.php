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

        $this->assertMethod('POST', $request);
        $this->assertQuery('merchantId', strval(env('VIVA_MERCHANT_ID')), $request);
        $this->assertJsonBody('amount', 1000, $request);
        $this->assertJsonBody('customerTrns', 'Short description of purchased items/services to display to your customer', $request);
        $this->assertJsonBody('customer', [
            'email' => 'johdoe@vivawallet.com',
            'fullName' => 'John Doe',
            'phone' => '+30999999999',
            'countryCode' => 'GB',
            'requestLang' => 'en-GB',
        ], $request);
        $this->assertJsonBody('paymentTimeOut', 300, $request);
        $this->assertJsonBody('currencyCode', '978', $request);
        $this->assertJsonBody('preauth', false, $request);
        $this->assertJsonBody('allowRecurring', false, $request);
        $this->assertJsonBody('maxInstallments', 12, $request);
        $this->assertJsonBody('paymentNotification', true, $request);
        $this->assertJsonBody('tipAmount', 100, $request);
        $this->assertJsonBody('disableExactAmount', false, $request);
        $this->assertJsonBody('disableCash', true, $request);
        $this->assertJsonBody('disableWallet', true, $request);
        $this->assertJsonBody('sourceCode', '1234', $request);
        $this->assertJsonBody('merchantTrns', 'Short description of items/services purchased by customer', $request);
        $this->assertJsonBody('tags', [
            'tags for grouping and filtering the transactions',
            'this tag can be searched on VivaWallet sales dashboard',
            'Sample tag 1',
            'Sample tag 2',
            'Another string',
        ], $request);
        $this->assertJsonBody('isvAmount', 1, $request);
        $this->assertJsonBody('resellerSourceCode', 'Default', $request);
        $this->assertSame('1272214778972604', $orderCode, 'The order code should be 1272214778972604');
    }
}
