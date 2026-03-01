<?php

namespace Sebdesign\VivaPayments\Services;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\VivaException;

class Card
{
    public function __construct(protected Client $client) {}

    /**
     * Create card token.
     *
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions/paths/~1acquiring~1v1~1cards~1tokens/post
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function createToken(string $transactionId, array $guzzleOptions = []): string
    {
        $parameters = ['transactionId' => $transactionId];

        $response = $this->client->post(
            $this->client->getApiUrl()->withPath('/acquiring/v1/cards/tokens'),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        /** @phpstan-ignore return.type */
        return $response['token'] ?? '';
    }
}
