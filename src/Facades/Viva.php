<?php

namespace Sebdesign\VivaPayments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sebdesign\VivaPayments\Services\Card cards()
 * @method static \Sebdesign\VivaPayments\Services\OAuth oauth()
 * @method static \Sebdesign\VivaPayments\Services\Order orders()
 * @method static \Sebdesign\VivaPayments\Services\Transaction transactions()
 * @method static \Sebdesign\VivaPayments\Services\Webhook webhooks()
 * @method static \Sebdesign\VivaPayments\Services\ISV isv()
 * @method static \Sebdesign\VivaPayments\Client withEnvironment(\Sebdesign\VivaPayments\Enums\Environment|string $environment)
 * @method static \Sebdesign\VivaPayments\Client withBasicAuthCredentials(string $merchantId, string $apiKey)
 * @method static \Sebdesign\VivaPayments\Client withOAuthCredentials(string $clientId, string $clientSecret)
 * @method static \Sebdesign\VivaPayments\Client withToken(string $token)
 *
 * @see \Sebdesign\VivaPayments\Client
 */
class Viva extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Sebdesign\VivaPayments\Client::class;
    }
}
