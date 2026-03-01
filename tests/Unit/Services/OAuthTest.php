<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Responses\AccessToken;
use Sebdesign\VivaPayments\Services\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(AccessToken::class)]
#[CoversClass(OAuth::class)]
class OAuthTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_requests_an_access_token_with_the_default_credentials(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjBEOEZCOEQ2RURFQ0Y1Qzk3RUY1MjdDMDYxNkJCMjMzM0FCNjVGOUYiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJEWS00MXUzczljbC05U2ZBWVd1eU16cTJYNTgifQ.eyJuYmYiOjE1NjAxNTc4MDQsImV4cCI6MTU2MDE2MTQwNCwiaXNzIjoiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20iLCJhdWQiOlsiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20vcmVzb3VyY2VzIiwiY29yZV9hcGkiXSwiY2xpZW50X2lkIjoiZ2VuZXJpY19hY3F1aXJpbmdfY2xpZW50LmFwcHMudml2YXBheW1lbnRzLmNvbSIsInNjb3BlIjpbInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkcyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkczp0b2tlbnMiXX0.GNjeRJhQQir3M_rqvjC0C9Up_pA2AFxlv9dhpr-7C-Lk0Xr5gJyGwgb0BD7Bvp2Oku-CjgG8tqE0s8KaWGHYIqGQyFJIUWiHWMejRKRqkuzt128NbThX7f4w-tN6DoyP1EouDhBsMs5BwrxOkbkIXtSjBxkE7jEOrRJ4YNAv-DjuDsPtAjC0cTLEDQBnMHLHAE-c2XHJ84I9WLFnOUX6-lwdwWuefv5o6BpvfNFC6y0mR-DcAi9KE82jRFVoY5G7xY6HQnS6RqaNDC5ifhdZKZcpgUxxdPTIWpS5L2F81RXsoMq3BSAWqvwuNeT8QTWDvtAsv_fgUABs06P7-slnvg',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'urn:viva:payments:core:api:test',
        ]);
        $this->mockRequests();

        $token = $this->client->withOAuthCredentials('client_id', 'client_secret')
            ->oauth()->requestToken();

        $request = $this->getLastRequest();
        $this->assertMethod('POST', $request);
        $this->assertHeader('Authorization', 'Basic '.base64_encode('client_id:client_secret'), $request);
        $this->assertBody('grant_type', 'client_credentials', $request);

        $this->assertEquals('eyJhbGciOiJSUzI1NiIsImtpZCI6IjBEOEZCOEQ2RURFQ0Y1Qzk3RUY1MjdDMDYxNkJCMjMzM0FCNjVGOUYiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJEWS00MXUzczljbC05U2ZBWVd1eU16cTJYNTgifQ.eyJuYmYiOjE1NjAxNTc4MDQsImV4cCI6MTU2MDE2MTQwNCwiaXNzIjoiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20iLCJhdWQiOlsiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20vcmVzb3VyY2VzIiwiY29yZV9hcGkiXSwiY2xpZW50X2lkIjoiZ2VuZXJpY19hY3F1aXJpbmdfY2xpZW50LmFwcHMudml2YXBheW1lbnRzLmNvbSIsInNjb3BlIjpbInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkcyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkczp0b2tlbnMiXX0.GNjeRJhQQir3M_rqvjC0C9Up_pA2AFxlv9dhpr-7C-Lk0Xr5gJyGwgb0BD7Bvp2Oku-CjgG8tqE0s8KaWGHYIqGQyFJIUWiHWMejRKRqkuzt128NbThX7f4w-tN6DoyP1EouDhBsMs5BwrxOkbkIXtSjBxkE7jEOrRJ4YNAv-DjuDsPtAjC0cTLEDQBnMHLHAE-c2XHJ84I9WLFnOUX6-lwdwWuefv5o6BpvfNFC6y0mR-DcAi9KE82jRFVoY5G7xY6HQnS6RqaNDC5ifhdZKZcpgUxxdPTIWpS5L2F81RXsoMq3BSAWqvwuNeT8QTWDvtAsv_fgUABs06P7-slnvg', $token->access_token);
        $this->assertEquals(3600, $token->expires_in);
        $this->assertEquals('Bearer', $token->token_type);
        $this->assertEquals('urn:viva:payments:core:api:test', $token->scope);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_requests_an_access_token_with_the_given_credentials(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjBEOEZCOEQ2RURFQ0Y1Qzk3RUY1MjdDMDYxNkJCMjMzM0FCNjVGOUYiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJEWS00MXUzczljbC05U2ZBWVd1eU16cTJYNTgifQ.eyJuYmYiOjE1NjAxNTc4MDQsImV4cCI6MTU2MDE2MTQwNCwiaXNzIjoiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20iLCJhdWQiOlsiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20vcmVzb3VyY2VzIiwiY29yZV9hcGkiXSwiY2xpZW50X2lkIjoiZ2VuZXJpY19hY3F1aXJpbmdfY2xpZW50LmFwcHMudml2YXBheW1lbnRzLmNvbSIsInNjb3BlIjpbInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkcyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkczp0b2tlbnMiXX0.GNjeRJhQQir3M_rqvjC0C9Up_pA2AFxlv9dhpr-7C-Lk0Xr5gJyGwgb0BD7Bvp2Oku-CjgG8tqE0s8KaWGHYIqGQyFJIUWiHWMejRKRqkuzt128NbThX7f4w-tN6DoyP1EouDhBsMs5BwrxOkbkIXtSjBxkE7jEOrRJ4YNAv-DjuDsPtAjC0cTLEDQBnMHLHAE-c2XHJ84I9WLFnOUX6-lwdwWuefv5o6BpvfNFC6y0mR-DcAi9KE82jRFVoY5G7xY6HQnS6RqaNDC5ifhdZKZcpgUxxdPTIWpS5L2F81RXsoMq3BSAWqvwuNeT8QTWDvtAsv_fgUABs06P7-slnvg',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'urn:viva:payments:core:api:test',
        ]);
        $this->mockRequests();

        $token = $this->client->oauth()->requestToken('foo', 'bar');

        $request = $this->getLastRequest();
        $this->assertMethod('POST', $request);
        $this->assertHeader('Authorization', 'Basic '.base64_encode('foo:bar'), $request);
        $this->assertBody('grant_type', 'client_credentials', $request);

        $this->assertEquals('eyJhbGciOiJSUzI1NiIsImtpZCI6IjBEOEZCOEQ2RURFQ0Y1Qzk3RUY1MjdDMDYxNkJCMjMzM0FCNjVGOUYiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJEWS00MXUzczljbC05U2ZBWVd1eU16cTJYNTgifQ.eyJuYmYiOjE1NjAxNTc4MDQsImV4cCI6MTU2MDE2MTQwNCwiaXNzIjoiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20iLCJhdWQiOlsiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20vcmVzb3VyY2VzIiwiY29yZV9hcGkiXSwiY2xpZW50X2lkIjoiZ2VuZXJpY19hY3F1aXJpbmdfY2xpZW50LmFwcHMudml2YXBheW1lbnRzLmNvbSIsInNjb3BlIjpbInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkcyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkczp0b2tlbnMiXX0.GNjeRJhQQir3M_rqvjC0C9Up_pA2AFxlv9dhpr-7C-Lk0Xr5gJyGwgb0BD7Bvp2Oku-CjgG8tqE0s8KaWGHYIqGQyFJIUWiHWMejRKRqkuzt128NbThX7f4w-tN6DoyP1EouDhBsMs5BwrxOkbkIXtSjBxkE7jEOrRJ4YNAv-DjuDsPtAjC0cTLEDQBnMHLHAE-c2XHJ84I9WLFnOUX6-lwdwWuefv5o6BpvfNFC6y0mR-DcAi9KE82jRFVoY5G7xY6HQnS6RqaNDC5ifhdZKZcpgUxxdPTIWpS5L2F81RXsoMq3BSAWqvwuNeT8QTWDvtAsv_fgUABs06P7-slnvg', $token->access_token);
        $this->assertEquals(3600, $token->expires_in);
        $this->assertEquals('Bearer', $token->token_type);
        $this->assertEquals('urn:viva:payments:core:api:test', $token->scope);
    }
}
