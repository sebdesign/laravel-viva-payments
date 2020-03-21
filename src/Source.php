<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class Source
{
    const ENDPOINT = '/api/sources/';

    /**
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \Sebdesign\VivaPayments\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a payment source.
     *
     * @param  string $name    A meaningful name that will help you identify the source in Web Self Care environment
     * @param  string $code    A unique code that is exchanged between your application and the API
     * @param  string $url     The primary domain of your site WITH protocol scheme (http/https)
     * @param  string $fail    The relative path url your client will end up to, after a failed transaction
     * @param  string $success The relative path url your client will end up to, after a successful transaction
     * @return \stdClass
     */
    public function create(string $name, string $code, $url, string $fail, string $success)
    {
        $uri = new Uri($url);

        return $this->client->post(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                'Name'          => $name,
                'SourceCode'    => $code,
                'Domain'        => $this->getDomain($uri),
                'isSecure'      => $this->isSecure($uri),
                'PathFail'      => $fail,
                'PathSuccess'   => $success,
            ],
        ]);
    }

    /**
     * Get the domain of the given URL.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return string
     */
    protected function getDomain(UriInterface $uri): string
    {
        return $uri->getHost();
    }

    /**
     * Check if the given URL has an https:// protocol scheme.
     *
     * @param  \Psr\Http\Message\UriInterface  $uri
     * @return bool
     */
    protected function isSecure(UriInterface $uri): bool
    {
        return strtolower($uri->getScheme()) === 'https';
    }
}
