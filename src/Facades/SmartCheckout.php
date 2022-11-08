<?php

namespace Sebdesign\VivaPayments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sebdesign\VivaPayments\Card cards()
 * @method static \Sebdesign\VivaPayments\Order orders()
 * @method static \Sebdesign\VivaPayments\Transaction transactions()
 * @method static \Sebdesign\VivaPayments\Webhook webhooks()
 * @method static \Sebdesign\VivaPayments\Webhook withEnvironment(\Sebdesign\VivaPayments\Enums\Environment|string $environment)
 * @method static \Sebdesign\VivaPayments\Webhook withBasicAuthCredentials(string $merchantId, string $apiKey)
 * @method static \Sebdesign\VivaPayments\Webhook withOAuthCredentials(string $clientId, string $clientSecret)
 * @method static \Sebdesign\VivaPayments\Webhook withToken(string $token)
 */
class SmartCheckout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return \Sebdesign\VivaPayments\Client::class;
    }
}
