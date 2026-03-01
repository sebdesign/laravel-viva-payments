<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Events\TransactionFailed;
use Sebdesign\VivaPayments\Events\TransactionPaymentCreated;
use Sebdesign\VivaPayments\Events\WebhookEvent;
use Sebdesign\VivaPayments\Http\Controllers\WebhookController;
use Sebdesign\VivaPayments\Services\Webhook;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(WebhookController::class)]
#[CoversClass(WebhookEvent::class)]
#[CoversClass(TransactionPaymentCreated::class)]
#[CoversClass(TransactionFailed::class)]
class WebhookControllerTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_verifies_a_webhook(): void
    {
        $this->mockJsonResponses(['Key' => 'foo']);

        $webhook = new Webhook($this->client);

        $controller = new WebhookController;

        $response = $controller->verify($webhook);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Key' => 'foo'], $response->getData(assoc: true));
    }

    #[Test]
    public function it_handles_a_notification_event(): void
    {
        Event::fake();

        $controller = new WebhookController;

        $event = strval(file_get_contents(__DIR__.'/../Stubs/transaction-price-calculated.json'));

        $request = Request::create('/', 'POST', content: $event);

        $response = $controller->handle($request);

        self::assertEquals(200, $response->getStatusCode());

        Event::assertDispatched(WebhookEvent::class);
    }

    #[Test]
    public function it_handles_a_create_transaction_notification_event(): void
    {
        Event::fake();

        $controller = new WebhookController;

        $event = strval(file_get_contents(__DIR__.'/../Stubs/transaction-payment-created.json'));

        $request = Request::create('/', 'POST', content: $event);

        $response = $controller->handle($request);

        self::assertEquals(200, $response->getStatusCode());

        Event::assertDispatched(WebhookEvent::class);
        Event::assertDispatched(TransactionPaymentCreated::class);
    }

    #[Test]
    public function it_handles_a_transaction_failed_notification_event(): void
    {
        Event::fake();

        $controller = new WebhookController;

        $event = strval(file_get_contents(__DIR__.'/../Stubs/transaction-failed.json'));

        $request = Request::create('/', 'POST', content: $event);

        $response = $controller->handle($request);

        self::assertEquals(200, $response->getStatusCode());

        Event::assertDispatched(WebhookEvent::class);
        Event::assertDispatched(TransactionFailed::class);
    }
}
