<?php

namespace Sebdesign\VivaPayments;

class Webhook
{
    const ENDPOINT = '/api/messages/config/token/';

    /**
     * Create Transaction event.
     */
    const CREATE_TRANSACTION = 1796;

    /**
     * Cancel/Refund Transaction event.
     */
    const REFUND_TRANSACTION = 1797;

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
     * Get a webhook authorization code.
     *
     * @return \stdClass
     */
    public function getAuthorizationCode()
    {
        return $this->client->get(self::ENDPOINT);
    }
}
