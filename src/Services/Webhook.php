<?php

namespace Sebdesign\VivaPayments\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Responses;
use Sebdesign\VivaPayments\VivaException;

class Webhook
{
    public function __construct(protected Client $client) {}

    /**
     * Get a webhook authorization code.
     *
     * @see https://developer.viva.com/webhooks-for-payments/#generate-a-webhook-verification-key
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function getVerificationKey(array $guzzleOptions = []): Responses\WebhookVerificationKey
    {
        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/messages/config/token'),
            array_merge_recursive(
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return Responses\WebhookVerificationKey::from($response);
    }
}
