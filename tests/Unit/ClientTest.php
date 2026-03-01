<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Enums\Environment;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(VivaException::class)]
class ClientTest extends TestCase
{
    #[Test]
    public function it_gets_the_demo_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Demo)->getUrl();

        $this->assertEquals(Client::DEMO_URL, $url, 'The URL should be '.Client::DEMO_URL);
    }

    #[Test]
    public function it_gets_the_production_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Production)->getUrl();

        $this->assertEquals(Client::PRODUCTION_URL, $url, 'The URL should be '.Client::PRODUCTION_URL);
    }

    #[Test]
    public function it_gets_the_demo_accounts_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Demo)->getAccountsUrl();

        $this->assertEquals(Client::DEMO_ACCOUNTS_URL, $url, 'The URL should be '.Client::DEMO_ACCOUNTS_URL);
    }

    #[Test]
    public function it_gets_the_production_accounts_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Production)->getAccountsUrl();

        $this->assertEquals(Client::PRODUCTION_ACCOUNTS_URL, $url, 'The URL should be '.Client::PRODUCTION_ACCOUNTS_URL);
    }

    #[Test]
    public function it_gets_the_demo_api_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Demo)->getApiUrl();

        $this->assertEquals(Client::DEMO_API_URL, $url, 'The URL should be '.Client::DEMO_API_URL);
    }

    #[Test]
    public function it_gets_the_production_api_url(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $url = $client->withEnvironment(Environment::Production)->getApiUrl();

        $this->assertEquals(Client::PRODUCTION_API_URL, $url, 'The URL should be '.Client::PRODUCTION_API_URL);
    }

    #[Test]
    public function it_authenticates_with_basic_auth(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $basic = $client->authenticateWithBasicAuth();

        $this->assertEquals(config('services.viva.merchant_id'), $basic['auth'][0]);
        $this->assertEquals(config('services.viva.api_key'), $basic['auth'][1]);
    }

    #[Test]
    public function it_sets_the_basic_auth_credentials(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $client->withBasicAuthCredentials('foo', 'bar');

        $basic = $client->authenticateWithBasicAuth();

        $this->assertEquals(['foo', 'bar'], $basic['auth']);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_authenticates_with_bearer_token(): void
    {
        /** @var Client */
        $client = $this->app?->make(Client::class);

        $bearer = $client->withToken('foo', Carbon::now()->addHour())->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => ['Authorization' => 'Bearer foo'],
        ], $bearer);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_fetches_a_new_token_when_no_token_is_cached(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'new-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ]);

        $bearer = $this->client->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => ['Authorization' => 'Bearer new-token'],
        ], $bearer);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_fetches_a_new_token_when_token_expiry_is_null(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'new-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ]);

        $bearer = $this->client->withToken('old-token')->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => ['Authorization' => 'Bearer new-token'],
        ], $bearer);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_fetches_a_new_token_when_the_token_has_expired(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'new-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ]);

        $bearer = $this->client
            ->withToken('expired-token', Carbon::now()->subSecond())
            ->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => ['Authorization' => 'Bearer new-token'],
        ], $bearer);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_caches_the_token_and_does_not_re_request_it(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'cached-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ]);
        $this->mockRequests();

        $firstBearer = $this->client->authenticateWithBearerToken();
        $secondBearer = $this->client->authenticateWithBearerToken();

        $this->assertEquals($firstBearer, $secondBearer);
        $this->assertCount(1, $this->history, 'The token should only be fetched once.');
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_subtracts_60_seconds_from_the_token_expiry(): void
    {
        Carbon::setTestNow('2026-01-01 00:00:00');

        $this->mockJsonResponses(
            [
                'access_token' => 'first-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
            [
                'access_token' => 'second-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
        );

        // Initial fetch: caches 'first-token' with expiry at now + (3600 - 60) = now + 3540s
        $this->client->authenticateWithBearerToken();

        // 1 second before the adjusted expiry: token should still be valid
        Carbon::setTestNow('2026-01-01 00:58:59');
        $bearer = $this->client->authenticateWithBearerToken();
        $this->assertEquals(
            ['headers' => ['Authorization' => 'Bearer first-token']],
            $bearer,
            'Token should still be cached before the adjusted expiry.'
        );

        // At exactly the adjusted expiry: token should be refreshed
        Carbon::setTestNow('2026-01-01 00:59:00');
        $bearer = $this->client->authenticateWithBearerToken();
        $this->assertEquals(
            ['headers' => ['Authorization' => 'Bearer second-token']],
            $bearer,
            'Token should be refreshed at the adjusted expiry time.'
        );

        Carbon::setTestNow();
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_sets_the_oauth_credentials(): void
    {
        $this->mockJsonResponses([
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjBEOEZCOEQ2RURFQ0Y1Qzk3RUY1MjdDMDYxNkJCMjMzM0FCNjVGOUYiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJEWS00MXUzczljbC05U2ZBWVd1eU16cTJYNTgifQ.eyJuYmYiOjE1NjAxNTc4MDQsImV4cCI6MTU2MDE2MTQwNCwiaXNzIjoiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20iLCJhdWQiOlsiaHR0cHM6Ly9kZW1vLWFjY291bnRzLnZpdmFwYXltZW50cy5jb20vcmVzb3VyY2VzIiwiY29yZV9hcGkiXSwiY2xpZW50X2lkIjoiZ2VuZXJpY19hY3F1aXJpbmdfY2xpZW50LmFwcHMudml2YXBheW1lbnRzLmNvbSIsInNjb3BlIjpbInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkcyIsInVybjp2aXZhOnBheW1lbnRzOmNvcmU6YXBpOmFjcXVpcmluZzpjYXJkczp0b2tlbnMiXX0.GNjeRJhQQir3M_rqvjC0C9Up_pA2AFxlv9dhpr-7C-Lk0Xr5gJyGwgb0BD7Bvp2Oku-CjgG8tqE0s8KaWGHYIqGQyFJIUWiHWMejRKRqkuzt128NbThX7f4w-tN6DoyP1EouDhBsMs5BwrxOkbkIXtSjBxkE7jEOrRJ4YNAv-DjuDsPtAjC0cTLEDQBnMHLHAE-c2XHJ84I9WLFnOUX6-lwdwWuefv5o6BpvfNFC6y0mR-DcAi9KE82jRFVoY5G7xY6HQnS6RqaNDC5ifhdZKZcpgUxxdPTIWpS5L2F81RXsoMq3BSAWqvwuNeT8QTWDvtAsv_fgUABs06P7-slnvg',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'urn:viva:payments:core:api:test',
        ]);
        $this->mockRequests();

        $client = $this->client;

        $client->withOAuthCredentials('foo', 'bar')->oauth()->requestToken();

        $request = $this->getLastRequest();

        $this->assertHeader('Authorization', 'Basic '.base64_encode('foo:bar'), $request);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_throws_an_exception_when_it_cannot_decode_an_invalid_response(): void
    {
        $this->mockResponses([new Response(body: 'invalid')]);

        $this->expectException(VivaException::class);
        $this->expectExceptionMessage('Invalid response');

        $this->client->get('test');
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_throws_an_exception_when_it_cannot_decode_a_response(): void
    {
        $this->mockResponses([new Response(body: 'null')]);

        $this->expectException(VivaException::class);
        $this->expectExceptionMessage('Invalid response');

        $this->client->get('test');
    }

    /**
     * @throws \JsonException
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_decodes_a_json_response(): void
    {
        $json = json_encode([
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ], JSON_THROW_ON_ERROR);

        $this->mockResponses([
            new Response(body: $json),
        ]);

        $response = $this->client->get('test');

        $this->assertEquals(json_decode($json, associative: true), $response, 'The JSON response was not decoded.');
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_throws_an_exception_when_the_response_has_errors(): void
    {
        $this->mockJsonResponses([
            'ErrorCode' => 1,
            'ErrorText' => 'Some error occurred.',
        ]);

        $this->expectException(VivaException::class);

        $this->client->get('error');
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_sends_a_get_request(): void
    {
        $body = ['foo' => 'bar'];

        $this->mockJsonResponses($body);
        $this->mockRequests();

        $response = $this->client->get('test', ['query' => ['key' => 'value']]);

        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertPath('test', $request);
        $this->assertQuery('key', 'value', $request);
        $this->assertEquals($body, $response);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_sends_a_post_request(): void
    {
        $body = ['foo' => 'bar'];

        $this->mockJsonResponses($body);
        $this->mockRequests();

        $response = $this->client->post('test', ['json' => ['key' => 'value']]);

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertPath('test', $request);
        $this->assertJsonBody('key', 'value', $request);
        $this->assertEquals($body, $response);
    }
}
