<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Sebdesign\VivaPayments\Enums\Environment;

class Client
{
    /**
     * Demo environment URL.
     */
    public const DEMO_URL = 'https://demo.vivapayments.com';

    /**
     * Production environment URL.
     */
    public const PRODUCTION_URL = 'https://www.vivapayments.com';

    /**
     * Demo environment accounts URL.
     */
    public const DEMO_ACCOUNTS_URL = 'https://demo-accounts.vivapayments.com';

    /**
     * Production environment accounts URL.
     */
    public const PRODUCTION_ACCOUNTS_URL = 'https://accounts.vivapayments.com';

    /**
     * Demo environment URL.
     */
    public const DEMO_API_URL = 'https://demo-api.vivapayments.com';

    /**
     * Production environment URL.
     */
    public const PRODUCTION_API_URL = 'https://api.vivapayments.com';

    protected string $token;

    public function __construct(
        public readonly GuzzleClient $client,
        protected Environment $environment,
        protected string $merchantId,
        protected string $apiKey,
        protected string $clientId,
        protected string $clientSecret,
    ) {
    }

    /**
     * Request OAuth access tokens.
     */
    public function oauth(): OAuth
    {
        return new OAuth($this, $this->clientId, $this->clientSecret);
    }

    /**
     * Create card tokens.
     */
    public function cards(): Card
    {
        return new Card($this);
    }

    /**
     * Create payment orders.
     */
    public function orders(): Order
    {
        return new Order($this);
    }

    /**
     * Retrieve and create recurring transactions.
     */
    public function transactions(): Transaction
    {
        return new Transaction($this);
    }

    /**
     * Verity webhooks.
     */
    public function webhooks(): Webhook
    {
        return new Webhook($this);
    }

    /**
     * Make a GET request.
     *
     * @param  array<string,mixed>  $options
     * @return array<mixed>
     */
    public function get(string $url, array $options = []): array
    {
        $response = $this->client->get($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a POST request.
     *
     * @param  array<string,mixed>  $options
     * @return array<mixed>
     */
    public function post(string $url, array $options = []): array
    {
        $response = $this->client->post($url, $options);

        return $this->getBody($response);
    }

    /**
     * Get the response body.
     *
     * @return array<mixed>
     *
     * @throws \Sebdesign\VivaPayments\VivaException
     */
    protected function getBody(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        $decoded = json_decode(
            json: $body,
            associative: true,
            depth: 512,
            flags: JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new VivaException('Invalid response', 0);
        }

        if (isset($decoded['ErrorCode']) && $decoded['ErrorCode'] !== 0) {
            throw new VivaException($decoded['ErrorText'], $decoded['ErrorCode']);
        }

        return $decoded;
    }

    /**
     * Get the URL.
     */
    public function getUrl(): UriInterface
    {
        return new Uri(match ($this->environment) {
            Environment::Production => self::PRODUCTION_URL,
            Environment::Demo => self::DEMO_URL,
        });
    }

    /**
     * Get the accounts URL.
     */
    public function getAccountsUrl(): UriInterface
    {
        return new Uri(match ($this->environment) {
            Environment::Production => self::PRODUCTION_ACCOUNTS_URL,
            Environment::Demo => self::DEMO_ACCOUNTS_URL,
        });
    }

    /**
     * Get the API URL.
     */
    public function getApiUrl(): UriInterface
    {
        return new Uri(match ($this->environment) {
            Environment::Production => self::PRODUCTION_API_URL,
            Environment::Demo => self::DEMO_API_URL,
        });
    }

    /**
     * Authenticate using basic auth.
     *
     * @return array{auth:array{string,string}}
     */
    public function authenticateWithBasicAuth(): array
    {
        return [
            RequestOptions::AUTH => [$this->merchantId, $this->apiKey],
        ];
    }

    /**
     * Authenticate using the bearer token as an authorization header.
     *
     * @return array{headers:array{Authorization:string}};
     */
    public function authenticateWithBearerToken(): array
    {
        $token = $this->token ??= $this->oauth()->requestToken()->access_token;

        return [
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$token}",
            ],
        ];
    }

    /**
     * Use the production or demo environment.
     */
    public function withEnvironment(Environment|string $environment): self
    {
        $this->environment = is_string($environment) ? Environment::from($environment) : $environment;

        return $this;
    }

    /**
     * Use the given Merchant ID and API key for basic authentication.
     *
     * @see https://developer.vivawallet.com/getting-started/find-your-account-credentials/merchant-id-and-api-key/
     */
    public function withBasicAuthCredentials(
        #[\SensitiveParameter] string $merchantId,
        #[\SensitiveParameter] string $apiKey,
    ): self {
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Use the given client credentials to authenticate with OAuth 2.0.
     *
     * @see https://developer.vivawallet.com/getting-started/find-your-account-credentials/client-smart-checkout-credentials/
     */
    public function withOAuthCredentials(
        #[\SensitiveParameter] string $clientId,
        #[\SensitiveParameter] string $clientSecret,
    ): self {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Use the given access token to authenticate with OAuth 2.0.
     */
    public function withToken(#[\SensitiveParameter] string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
