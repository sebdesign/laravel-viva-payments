<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;

class Order
{
    const ENDPOINT = '/api/orders/';

    const PENDING = 0;
    const EXPIRED = 1;
    const CANCELED = 2;
    const PAID = 3;

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
     * Create a payment order.
     *
     * @param  int   $amount     amount in cents
     * @param  array $parameters optional parameters (Full list available here: https://github.com/VivaPayments/API/wiki/Optional-Parameters)
     * @return int
     */
    public function create(int $amount, array $parameters = [])
    {
        $response = $this->client->post(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => array_merge(['Amount' => $amount], $parameters),
        ]);

        return $response->OrderCode;
    }

    /**
     * Retrieve information about an order.
     *
     * @param  int $orderCode  The unique Payment Order ID.
     * @return \stdClass
     */
    public function get($orderCode)
    {
        return $this->client->get(self::ENDPOINT.$orderCode);
    }

    /**
     * Update certain information of an order.
     *
     * @param  int    $orderCode   The unique Payment Order ID.
     * @param  array  $parameters
     * @return \stdClass
     */
    public function update($orderCode, array $parameters)
    {
        return $this->client->patch(self::ENDPOINT.$orderCode, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => $parameters,
        ]);
    }

    /**
     * Cancel an order.
     *
     * @param  int $orderCode  The unique Payment Order ID.
     * @return \stdClass
     */
    public function cancel($orderCode)
    {
        return $this->client->delete(self::ENDPOINT.$orderCode);
    }

    /**
     * Get the checkout URL for an order.
     *
     * @param  int $orderCode  The unique Payment Order ID.
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getCheckoutUrl($orderCode)
    {
        return Uri::withQueryValue(
            $this->client->getUrl()->withPath('web/checkout'),
            'ref',
            $orderCode
        );
    }
}
