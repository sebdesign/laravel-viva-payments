<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Services\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/**
 * @covers \Sebdesign\VivaPayments\Client
 * @covers \Sebdesign\VivaPayments\Services\Order
 */
class OrderTest extends TestCase
{
    /**
     * @test
     *
     * @covers \Sebdesign\VivaPayments\Requests\CreatePaymentOrder
     * @covers \Sebdesign\VivaPayments\Requests\Customer
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_creates_a_payment_order(): void
    {
        $this->mockJsonResponses(['orderCode' => '1272214778972604']);
        $this->mockRequests();

        $this->client->withToken('test');

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
            paymentTimeout: 300,
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

        $this->assertMethod('POST', $request);
        $this->assertJsonBody('amount', 1000, $request);
        $this->assertJsonBody('customerTrns', 'Short description of purchased items/services to display to your customer', $request);
        $this->assertJsonBody('customer', [
            'email' => 'johdoe@vivawallet.com',
            'fullName' => 'John Doe',
            'phone' => '+30999999999',
            'countryCode' => 'GB',
            'requestLang' => 'en-GB',
        ], $request);
        $this->assertJsonBody('paymentTimeout', 300, $request);
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
        $this->assertJsonBody('cardTokens', ['ct_5d0a4e3a7e04469f82da228ca98fd661'], $request);
        $this->assertSame('1272214778972604', $orderCode, 'The order code should be 1272214778972604');
    }

    /**
     * @test
     */
    public function it_gets_a_redirect_url(): void
    {
        $this->mockJsonResponses([]);
        $this->mockRequests();

        $url = $this->client->orders()->redirectUrl(
            ref: '175936509216',
            color: '0000ff',
            paymentMethod: 23,
        );

        $this->assertEquals(Client::DEMO_URL.'/web/checkout?ref=175936509216&color=0000ff&paymentMethod=23', $url);
    }
}
