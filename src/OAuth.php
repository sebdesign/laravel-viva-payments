<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Responses\AccessToken;

class OAuth
{
    public function __construct(
        public readonly Client $client,
        public readonly string $clientId,
        public readonly string $clientSecret,
    ) {
    }

    /**
     * Request access token.
     *
     * @see https://developer.vivawallet.com/integration-reference/oauth2-authentication/
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional options for the Guzzle client
     */
    public function requestToken(
        #[\SensitiveParameter] ?string $clientId = null,
        #[\SensitiveParameter] ?string $clientSecret = null,
        array $guzzleOptions = []
    ): AccessToken {
        $parameters = ['grant_type' => 'client_credentials'];

        $response = $this->client->post(
            $this->client->getAccountsUrl()->withPath('/connect/token'),
            [
                RequestOptions::FORM_PARAMS => $parameters,
                RequestOptions::AUTH => [
                    $clientId ?? $this->clientId,
                    $clientSecret ?? $this->clientSecret,
                ],
                ...$guzzleOptions,
            ]
        );

        /** @phpstan-ignore-next-line */
        return new AccessToken(...$response);
    }
}
