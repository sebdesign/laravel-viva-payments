<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Sebdesign\VivaPayments\Enums\Environment;

class VivaPaymentsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $this->buildGuzzleClient(),
                Environment::from($app->make('config')->get('services.viva.environment')),
                merchantId: strval($app->make('config')->get('services.viva.merchant_id')),
                apiKey: strval($app->make('config')->get('services.viva.api_key')),
                clientId: strval($app->make('config')->get('services.viva.client_id')),
                clientSecret: strval($app->make('config')->get('services.viva.client_secret')),
            );
        });

        $this->app->bind(OAuth::class, function ($app) {
            return new OAuth(
                client: $app->make(Client::class),
                clientId: strval($app->make('config')->get('services.viva.client_id')),
                clientSecret: strval($app->make('config')->get('services.viva.client_secret')),
            );
        });
    }

    /**
     * Build the Guzzlehttp client.
     */
    protected function buildGuzzleClient(): GuzzleClient
    {
        return new GuzzleClient([
            'curl' => $this->curlDoesntUseNss()
                ? [CURLOPT_SSL_CIPHER_LIST => 'TLSv1.2']
                : [],
        ]);
    }

    /**
     * Check if cURL doens't use NSS.
     */
    protected function curlDoesntUseNss(): bool
    {
        $curl = curl_version();

        // @codeCoverageIgnoreStart
        if (! isset($curl['ssl_version'])) {
            return true;
        }
        // @codeCoverageIgnoreEnd

        return preg_match('/NSS/', $curl['ssl_version']) !== 1;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int,string>
     */
    public function provides()
    {
        return [Client::class, OAuth::class];
    }
}
