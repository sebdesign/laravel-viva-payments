<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Services\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(Order::class)]
#[CoversClass(CreatePaymentOrder::class)]
#[CoversClass(Customer::class)]
class OrderTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_a_payment_order(): void
    {
        $this->mockJsonResponses(['orderCode' => '1272214778972604']);
        $this->mockRequests();

        $this->client->withToken('test', Carbon::now()->addHour());

        $order = new Order($this->client);

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
            cardTokens: ['ct_5d0a4e3a7e04469f82da228ca98fd661'],
        ));

        $request = $this->getLastRequest();

        self::assertMethod('POST', $request);
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
        self::assertJsonBody('cardTokens', ['ct_5d0a4e3a7e04469f82da228ca98fd661'], $request);
        self::assertSame('1272214778972604', $orderCode, 'The order code should be 1272214778972604');
    }

    #[Test]
    public function it_gets_a_redirect_url(): void
    {
        $this->mockJsonResponses([]);
        $this->mockRequests();

        $url = $this->client->orders()->redirectUrl(
            ref: '175936509216',
            color: '0000ff',
            paymentMethod: 23,
        );

        self::assertEquals(Client::DEMO_URL.'/web/checkout?ref=175936509216&color=0000ff&paymentMethod=23', $url);
    }
}
