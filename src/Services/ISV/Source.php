<?php

namespace Sebdesign\VivaPayments\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests;
use Sebdesign\VivaPayments\VivaException;

class Source
{
    public function __construct(protected Client $client) {}

    /**
     * Add a new source for merchants.
     *
     * @see https://developer.viva.com/isv-partner-program/payment-isv-api/#tag/Sources/paths/~1api~1sources/post
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function create(Requests\CreateSource $source, array $guzzleOptions = []): void
    {
        $this->client->post(
            $this->client->getUrl()->withPath('/api/sources'),
            array_merge_recursive(
                [RequestOptions::JSON => $source],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions,
            )
        );
    }
}
