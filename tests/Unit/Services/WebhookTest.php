<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Responses\WebhookVerificationKey;
use Sebdesign\VivaPayments\Services\Webhook;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(Webhook::class)]
#[CoversClass(WebhookVerificationKey::class)]
final class WebhookTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_gets_an_authorization_code(): void
    {
        $this->mockJsonResponses(['Key' => 'foo']);
        $this->mockRequests();

        $verification = $this->client->webhooks()->getVerificationKey();
        $request = $this->getLastRequest();

        self::assertMethod('GET', $request);
        self::assertEquals('foo', $verification->Key);
    }
}
