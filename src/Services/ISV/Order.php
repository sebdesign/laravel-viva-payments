<?php

namespace Sebdesign\VivaPayments\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests;
use Sebdesign\VivaPayments\VivaException;

class Order
{
    public function __construct(protected Client $client) {}

    /**
     * Create payment order.
     *
     * @see https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Payments/paths/~1checkout~1v2~1isv~1orders/post
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function create(Requests\CreatePaymentOrder $order, array $guzzleOptions = []): string
    {
        $response = $this->client->post(
            $this->client->getApiUrl()->withPath('/checkout/v2/isv/orders'),
            array_merge_recursive(
                [RequestOptions::QUERY => ['merchantId' => $this->client->merchantId]],
                [RequestOptions::JSON => $order],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions,
            )
        );

        return fluent($response)->string('orderCode');
    }
}
