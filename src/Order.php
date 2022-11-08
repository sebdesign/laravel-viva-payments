<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;

class Order
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Payments/paths/~1checkout~1v2~1orders/post
     *
     * @param  array<string,mixed>  $guzzleOptions
     */
    public function create(CreatePaymentOrder $order, array $guzzleOptions = []): string
    {
        $response = $this->client->post(
            $this->client->getApiUrl()->withPath('/checkout/v2/orders'),
            array_merge_recursive(
                [RequestOptions::JSON => $order],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions,
            )
        );

        return strval($response['orderCode'] ?? '');
    }

    /**
     * Get the redirect URL to the Smart Checkout for an order.
     *
     * @see https://developer.vivawallet.com/smart-checkout/smart-checkout-integration/#step-2-redirect-the-customer-to-smart-checkout-to-pay-the-payment-order
     */
    public function redirectUrl(
        string $ref,
        ?string $color = null,
        ?int $paymentMethod = null,
    ): UriInterface {
        return Uri::withQueryValues(
            $this->client->getUrl()->withPath('/web/checkout'),
            array_map(strval(...), array_filter([
                'ref' => $ref,
                'color' => $color,
                'paymentMethod' => $paymentMethod,
            ], fn (int|string|null $value) => ! is_null($value)),
            ));
    }
}
