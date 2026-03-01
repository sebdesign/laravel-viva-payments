<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Services\Webhook;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Webhook::class)]
final class WebhookTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_gets_a_verification_key(): void
    {
        $verification = Viva::webhooks()->getVerificationKey();

        self::assertNotEmpty($verification->Key, "Failed asserting that '{$verification->Key}' is not empty.");
    }
}
