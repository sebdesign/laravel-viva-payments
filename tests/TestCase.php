<?php

namespace Sebdesign\VivaPayments\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Client.
     *
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Handler stack.
     *
     * @var \GuzzleHttp\HandlerStack
     */
    protected $handler;

    /**
     * History of requests.
     *
     * @var array
     */
    protected $history = [];

    /**
     * Responses.
     *
     * @var array
     */
    protected $responses = [];

    protected function getPackageProviders($app)
    {
        return [VivaPaymentsServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);
    }

    protected function mockRequests()
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
     * @param \GuzzleHttp\Psr7\Response[] $responses
     * @return void
     */
    protected function mockResponses(array $responses)
    {
        $mock = new MockHandler($responses);
        $this->handler = HandlerStack::create($mock);

        $this->makeClient();
    }

    /**
     * Make a client instance from a Guzzle handler.
     */
    protected function makeClient()
    {
        $mockClient = new GuzzleClient([
            'handler' => $this->handler,
            'base_uri' => Client::DEMO_URL,
            'curl' => [CURLOPT_SSL_CIPHER_LIST => 'TLSv1'],
            'auth' => [
                $this->app['config']['merchant_id'],
                $this->app['config']['api_key'],
            ],
        ]);

        $this->client = new Client($mockClient);
    }

    protected function mockJsonResponses(array $bodies)
    {
        $responses = array_map(function ($body) {
            return new Response(200, [], json_encode($body));
        }, $bodies);

        $this->mockResponses($responses);
    }

    public function assertPath(string $path, RequestInterface $request)
    {
        $this->assertEquals($path, $request->getUri()->getPath());

        return $this;
    }

    public function assertMethod(string $name, RequestInterface $request)
    {
        $this->assertEquals($name, $request->getMethod(), "The request method should be [{$name}].");

        return $this;
    }

    public function assertQuery(string $name, $value, RequestInterface $request)
    {
        $query = $request->getUri()->getQuery();

        parse_str($query, $output);

        $this->assertArrayHasKey(
            $name,
            $output,
            "Did not see expected query string parameter [{$name}] in [{$query}]."
        );

        $this->assertEquals(
            $value,
            $output[$name],
            "Query string parameter [{$name}] had value [{$output[$name]}], but expected [{$value}]."
        );

        return $this;
    }

    public function assertBody(string $name, $value, RequestInterface $request)
    {
        parse_str($request->getBody(), $body);

        $this->assertArrayHasKey($name, $body);

        $this->assertSame($value, $body[$name]);

        return $this;
    }

    public function assertJsonBody(string $name, $value, RequestInterface $request)
    {
        $body = json_decode($request->getBody(), true);

        $this->assertArrayHasKey($name, $body);

        $this->assertSame($value, $body[$name]);

        return $this;
    }

    public function assertHeader(string $name, $value, RequestInterface $request)
    {
        $this->assertTrue($request->hasHeader($name), "The header [{$name}] should be passed as a header.");

        $this->assertEquals($value, $request->getHeader($name)[0], "The header [{$name}] should be [{$value}].");

        return $this;
    }
}
