<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Illuminate\Http\Request;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Webhook;
use Sebdesign\VivaPayments\WebhookController;

class WebhookControllerTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_verifies_a_webhook()
    {
        $verification = ['foo' => 'bar'];

        $this->mockJsonResponses([$verification]);

        $webhook = new Webhook($this->client);

        $controller = new WebhookTestController($webhook);

        $request = Request::create('/', 'GET');

        $response = $controller->handle($request);

        $this->assertEquals($verification, $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_handles_a_notification_event()
    {
        $webhook = app(Webhook::class);
        $controller = new WebhookTestController($webhook);

        $event = [
            'EventTypeId' => 1795,
            'foo' => 'bar',
        ];

        $request = Request::create('/', 'POST', $event);

        $response = $controller->handle($request);

        $this->assertStringEndsWith('handleEventNotification', $response['handler']);
        $this->assertEquals(1795, $response['EventTypeId']);
        $this->assertEquals('bar', $response['foo']);
    }

    /**
     * @test
     * @group unit
     */
    public function it_handles_a_create_transaction_notification_event()
    {
        $webhook = app(Webhook::class);
        $controller = new WebhookTestController($webhook);

        $event = [
            'EventTypeId' => Webhook::CREATE_TRANSACTION,
            'foo' => 'bar',
        ];

        $request = Request::create('/', 'POST', $event);

        $response = $controller->handle($request);

        $this->assertStringEndsWith('handleCreateTransaction', $response['handler']);
        $this->assertEquals(Webhook::CREATE_TRANSACTION, $response['EventTypeId']);
        $this->assertEquals('bar', $response['foo']);
    }

    /**
     * @test
     * @group unit
     */
    public function it_handles_a_refund_transaction_notification_event()
    {
        $webhook = app(Webhook::class);
        $controller = new WebhookTestController($webhook);

        $event = [
            'EventTypeId' => Webhook::REFUND_TRANSACTION,
            'foo' => 'bar',
        ];

        $request = Request::create('/', 'POST', $event);

        $response = $controller->handle($request);

        $this->assertStringEndsWith('handleRefundTransaction', $response['handler']);
        $this->assertEquals(Webhook::REFUND_TRANSACTION, $response['EventTypeId']);
        $this->assertEquals('bar', $response['foo']);
    }
}

class WebhookTestController extends WebhookController
{
    protected function handleCreateTransaction(Request $request)
    {
        $request['handler'] = __METHOD__;

        return $request->all();
    }

    protected function handleRefundTransaction(Request $request)
    {
        $request['handler'] = __METHOD__;

        return $request->all();
    }

    protected function handleEventNotification(Request $request)
    {
        $request['handler'] = __METHOD__;

        return $request->all();
    }
}
