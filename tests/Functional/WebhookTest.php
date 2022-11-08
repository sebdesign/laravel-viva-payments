<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Webhook;

/** @covers \Sebdesign\VivaPayments\Webhook */
class WebhookTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_gets_a_verification_key(): void
    {
        $verification = app(Webhook::class)->getVerificationKey();

        $this->assertObjectHasAttribute('Key', $verification);
        $this->assertNotEmpty($verification->Key, "Failed asserting that '{$verification->Key}' is not empty.");
    }
}
