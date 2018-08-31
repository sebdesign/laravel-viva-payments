<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

class ClientTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_gets_the_url_from_guzzle_configuration()
    {
        $client = new Client(new GuzzleClient(['base_uri' => Client::PRODUCTION_URL]));

        $this->assertEquals(Client::PRODUCTION_URL, $client->getUrl(), 'The URL should be '.Client::PRODUCTION_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_decodes_a_json_response()
    {
        $json = json_encode([
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ]);

        $this->mockResponses([
            new Response(200, [], $json),
        ]);

        $order = new Order($this->client);

        $response = $order->get(42);

        $this->assertEquals(json_decode($json), $response, 'The JSON response was not decoded.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_throws_an_exception()
    {
        $success = [
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ];

        $failure = [
            'ErrorCode' => 1,
            'ErrorText' => 'Some error occurred.',
        ];

        $this->mockJsonResponses(compact('success', 'failure'));

        $order = new Order($this->client);

        $order->get(42);

        $this->expectException(VivaException::class);

        $order->get(43);
    }
}
