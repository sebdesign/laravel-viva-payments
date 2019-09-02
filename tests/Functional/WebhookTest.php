<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Webhook;

class WebhookTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_gets_an_authorization_code()
    {
        $code = app(Webhook::class)->getAuthorizationCode();

        $this->assertObjectHasAttribute('Key', $code);
        $this->assertIsString($code->Key);
    }
}
