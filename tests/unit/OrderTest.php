<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Test\TestCase;

class OrderTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_an_order()
    {
        $this->mockJsonResponses([['OrderCode' => 175936509216]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $orderCode = $order->create(30, ['CustomerTrns' => 'Your reference']);
        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertBody('Amount', '30', $request);
        $this->assertBody('CustomerTrns', 'Your reference', $request);
        $this->assertEquals('175936509216', $orderCode, 'The order code should be 175936509216');
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_an_order()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $order = new Order($this->client);

        $response = $order->get(175936509216);
        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertPath('/api/orders/175936509216', $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response, 'The response is not correct.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_updates_an_order()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $parameters = ['Amount' => 50];
        $orderCode = $order->update(175936509216, $parameters);
        $request = $this->getLastRequest();

        $this->assertMethod('PATCH', $request);
        $this->assertPath('/api/orders/175936509216', $request);
        $this->assertBody('Amount', '50', $request);
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_an_order()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $response = $order->cancel(175936509216);
        $request = $this->getLastRequest();

        $this->assertMethod('DELETE', $request);
        $this->assertPath('/api/orders/175936509216', $request);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_a_checkout_url()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);
        $url = $order->getCheckoutUrl(175936509216);

        $this->assertEquals(Client::DEMO_URL.'/web/checkout?ref=175936509216', $url);
    }
}
