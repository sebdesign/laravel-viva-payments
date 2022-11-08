<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Webhook;

/** @covers \Sebdesign\VivaPayments\Webhook */
class WebhookTest extends TestCase
{
    /**
     * @test
     * @group unit
     * @covers \Sebdesign\VivaPayments\Responses\WebhookVerificationKey
     */
    public function it_gets_an_authorization_code(): void
    {
        $this->mockJsonResponses(['Key' => 'foo']);
        $this->mockRequests();

        $webhook = new Webhook($this->client);

        $verification = $webhook->getVerificationKey();
        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertEquals('foo', $verification->Key);
    }
}
