<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Responses\AccessToken;
use Sebdesign\VivaPayments\Services\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(OAuth::class)]
#[CoversClass(AccessToken::class)]
class OAuthTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    #[DoesNotPerformAssertions]
    public function it_requests_an_access_token_with_the_default_credentials(): void
    {
        Viva::oauth()->requestToken();
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    #[DoesNotPerformAssertions]
    public function it_requests_an_access_token_with_the_given_credentials(): void
    {
        Viva::oauth()->requestToken(
            clientId: strval(env('VIVA_CLIENT_ID')),
            clientSecret: strval(env('VIVA_CLIENT_SECRET')),
        );
    }
}
