<?php

namespace Sebdesign\VivaPayments\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Enums\Environment;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected Client $client;

    protected HandlerStack $handler;

    protected $loadEnvironmentVariables = true;

    /**
     * History of requests.
     *
     * @var array<int,array{request:RequestInterface,response:ResponseInterface}>
     */
    protected array $history = [];

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, string>
     */
    protected function getPackageProviders($app)
    {
        return [VivaPaymentsServiceProvider::class];
    }

    protected function mockRequests(): void
    {
        $history = Middleware::history($this->history);

        $this->handler->push($history);
    }

    protected function getLastRequest(): RequestInterface
    {
        return $this->history[0]['request'];
    }

    /**
     * Mock responses.
     *
     * @param  \GuzzleHttp\Psr7\Response[]  $responses
     */
    protected function mockResponses(array $responses): void
    {
        $mock = new MockHandler($responses);
        $this->handler = HandlerStack::create($mock);

        $this->makeClient();
    }

    /**
     * Make a client instance from a Guzzle handler.
     */
    protected function makeClient(): void
    {
        $mockClient = new GuzzleClient([
            'handler' => $this->handler,
            'curl' => [CURLOPT_SSL_CIPHER_LIST => 'TLSv1'],
        ]);

        $this->client = new Client(
            $mockClient,
            Environment::Demo,
            strval(config('services.viva.merchant_id')),
            strval(config('services.viva.api_key')),
            strval(config('services.viva.client_id')),
            strval(config('services.viva.client_secret')),
        );
    }

    /** @param  array<mixed>  ...$bodies */
    protected function mockJsonResponses(array ...$bodies): void
    {
        $responses = array_map(function (array $body) {
            return new Response(body: json_encode($body, JSON_THROW_ON_ERROR));
        }, $bodies);

        $this->mockResponses($responses);
    }

    public function assertPath(string $path, RequestInterface $request): self
    {
        $this->assertEquals($path, $request->getUri()->getPath());

        return $this;
    }

    public function assertMethod(string $name, RequestInterface $request): self
    {
        $this->assertEquals($name, $request->getMethod(), "The request method should be [{$name}].");

        return $this;
    }

    public function assertQuery(string $name, string $value, RequestInterface $request): self
    {
        $query = $request->getUri()->getQuery();

        parse_str($query, $output);

        $this->assertArrayHasKey(
            $name,
            $output,
            "Did not see expected query string parameter [{$name}] in [{$query}]."
        );

        $this->assertIsString($output[$name]);

        $this->assertEquals(
            $value,
            $output[$name],
            "Query string parameter [{$name}] had value [{$output[$name]}], but expected [{$value}]."
        );

        return $this;
    }

    public function assertBody(string $name, string $value, RequestInterface $request): self
    {
        parse_str($request->getBody(), $body);

        $this->assertArrayHasKey($name, $body);

        $this->assertSame($value, $body[$name]);

        return $this;
    }

    /** @param  array<mixed>|string|int|bool  $value */
    public function assertJsonBody(string $name, mixed $value, RequestInterface $request): self
    {
        $body = json_decode($request->getBody(), associative: true);

        $this->assertIsArray($body);

        $this->assertArrayHasKey($name, $body);

        $this->assertSame($value, $body[$name]);

        return $this;
    }

    public function assertHeader(string $name, string $value, RequestInterface $request): self
    {
        $this->assertTrue($request->hasHeader($name), "The header [{$name}] should be passed as a header.");

        $this->assertEquals($value, $request->getHeader($name)[0], "The header [{$name}] should be [{$value}].");

        return $this;
    }
}
