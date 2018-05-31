<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class Client
{
    /**
     * Demo environment URL.
     */
    const DEMO_URL = 'https://demo.vivapayments.com';

    /**
     * Production environment URL.
     */
    const PRODUCTION_URL = 'https://www.vivapayments.com';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \GuzzleHttp\ClientInterface   $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Make a GET request.
     *
     * @param  string $url
     * @param  array  $options
     * @return object
     */
    public function get($url, array $options = [])
    {
        $response = $this->client->get($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a POST request.
     *
     * @param  string $url
     * @param  array  $options
     * @return object
     */
    public function post($url, array $options = [])
    {
        $response = $this->client->post($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a PATCH request.
     *
     * @param  string $url
     * @param  array  $options
     * @return object
     */
    public function patch($url, array $options = [])
    {
        $response = $this->client->patch($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a DELETE request.
     *
     * @param  string $url
     * @param  array  $options
     * @return object
     */
    public function delete($url, array $options = [])
    {
        $response = $this->client->delete($url, $options);

        return $this->getBody($response);
    }

    /**
     * Get the response body.
     *
     * @param  \GuzzleHttp\Psr7\Response $response
     * @return mixed
     *
     * @throws \Sebdesign\VivaPayments\VivaException
     */
    protected function getBody(Response $response)
    {
        $body = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);

        if (isset($body->ErrorCode) && $body->ErrorCode !== 0) {
            throw new VivaException($body->ErrorText, $body->ErrorCode);
        }

        return $body;
    }

    /**
     * Get the URL.
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUrl()
    {
        return $this->client->getConfig('base_uri');
    }

    /**
     * Get the Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
