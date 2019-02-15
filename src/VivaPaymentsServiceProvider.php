<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class VivaPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php',
            'services'
        );

        $this->app->singleton(Client::class, function ($app) {
            return new Client($this->buildGuzzleClient($app));
        });
    }

    /**
     * Build the Guzzlehttp client.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return \GuzzleHttp\Client
     */
    protected function buildGuzzleClient($app)
    {
        $config = $app['config']->get('services.viva');

        return new GuzzleClient([
            'base_uri' => $this->getUrl($config['environment']),
            'curl' => $this->curlDoesntUseNss()
                ? [CURLOPT_SSL_CIPHER_LIST => 'TLSv1']
                : [],
            \GuzzleHttp\RequestOptions::AUTH => [
                $config['merchant_id'],
                $config['api_key'],
            ],
        ]);
    }

    /**
     * Check if cURL doens't use NSS.
     *
     * @return bool
     */
    protected function curlDoesntUseNss()
    {
        $curl = curl_version();

        return ! preg_match('/NSS/', $curl['ssl_version']);
    }

    /**
     * Get the URL based on the environment.
     *
     * @param  string $environment
     * @return string
     */
    protected function getUrl($environment)
    {
        if ($environment === 'production') {
            return Client::PRODUCTION_URL;
        }

        if ($environment === 'demo') {
            return Client::DEMO_URL;
        }

        throw new \InvalidArgumentException('The Viva Payments environment must be demo or production.');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}
