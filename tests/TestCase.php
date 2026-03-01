<?php

namespace Sebdesign\VivaPayments\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Fluent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Enums\Environment;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

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
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [LaravelDataServiceProvider::class, VivaPaymentsServiceProvider::class];
    }

    protected function mockRequests(): void
    {
        /** @throws never */
        /** @phpstan-ignore assign.propertyType */
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

        /** @phpstan-ignore argument.type */
        $config = new Fluent(config('services.viva'));

        $this->client = new Client(
            $mockClient,
            Environment::Demo,
            $config->string('merchant_id'),
            $config->string('api_key'),
            $config->string('client_id'),
            $config->string('client_secret'),
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
        self::assertEquals($path, $request->getUri()->getPath());

        return $this;
    }

    public static function assertMethod(string $name, RequestInterface $request): void
    {
        self::assertEquals($name, $request->getMethod(), "The request method should be [{$name}].");
    }

    public static function assertQuery(string $name, mixed $expectedValue, RequestInterface $request): void
    {
        self::assertIsString($expectedValue, "The expected value for query string parameter [{$name}] should be a string, but got [".get_debug_type($expectedValue).'].');

        $query = $request->getUri()->getQuery();

        parse_str($query, $output);

        self::assertArrayHasKey(
            $name,
            $output,
            "Did not see expected query string parameter [{$name}] in [{$query}]."
        );

        $value = $output[$name];

        self::assertIsString($value, "Query string parameter [{$name}] should be a string, but got [".get_debug_type($value).'].');

        self::assertEquals(
            $expectedValue,
            $value,
            "Query string parameter [{$name}] had value [{$value}], but expected [{$expectedValue}]."
        );
    }

    public static function assertBody(string $name, string $value, RequestInterface $request): void
    {
        parse_str($request->getBody(), $body);

        self::assertArrayHasKey($name, $body);

        self::assertSame($value, $body[$name]);
    }

    /** @param  array<mixed>|string|int|bool  $value */
    public static function assertJsonBody(string $name, mixed $value, RequestInterface $request): void
    {
        $body = json_decode($request->getBody(), associative: true);

        self::assertIsArray($body);

        self::assertArrayHasKey($name, $body);

        self::assertSame($value, $body[$name]);
    }

    public static function assertHeader(string $name, string $value, RequestInterface $request): void
    {
        self::assertTrue($request->hasHeader($name), "The header [{$name}] should be passed as a header.");

        self::assertEquals($value, $request->getHeader($name)[0], "The header [{$name}] should be [{$value}].");
    }
}
