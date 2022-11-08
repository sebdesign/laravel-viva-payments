<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\Responses\AccessToken;
use Sebdesign\VivaPayments\Test\TestCase;

/** @covers \Sebdesign\VivaPayments\OAuth */
class OAuthTest extends TestCase
{
    protected OAuth $oauth;

    protected function setUp(): void
    {
        parent::setUp();

        /** @phpstan-ignore-next-line */
        $this->oauth = $this->app->make(OAuth::class);
    }

    /**
     * @test
     * @group functional
     */
    public function it_requests_an_access_token_with_the_default_credentials(): void
    {
        $token = $this->oauth->requestToken();

        $this->assertInstanceOf(AccessToken::class, $token);
    }

    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Responses\AccessToken
     */
    public function it_requests_an_access_token_with_the_given_credentials(): void
    {
        $token = $this->oauth->requestToken(
            clientId: strval(env('VIVA_CLIENT_ID')),
            clientSecret: strval(env('VIVA_CLIENT_SECRET')),
        );

        $this->assertInstanceOf(AccessToken::class, $token);
    }
}
