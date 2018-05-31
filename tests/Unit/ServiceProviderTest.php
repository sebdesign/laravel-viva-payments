<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use InvalidArgumentException;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

class ServiceProviderTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_is_deferred()
    {
        $provider = $this->app->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertTrue($provider->isDeferred());
    }

    /**
     * @test
     * @group unit
     */
    public function it_merges_the_configuration()
    {
        $config = $this->app['config']->get('services.viva');

        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('api_key', $config);
        $this->assertArrayHasKey('merchant_id', $config);
        $this->assertArrayHasKey('public_key', $config);
        $this->assertArrayHasKey('environment', $config);
    }

    /**
     * @test
     * @group unit
     */
    public function it_provides_the_client()
    {
        $provider = $this->app->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertContains(Client::class, $provider->provides());
    }

    /**
     * @test
     * @group unit
     */
    public function it_resolves_the_client_as_a_singleton()
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceof(Client::class, $client);
        $this->assertTrue($this->app->isShared(Client::class));
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_demo_url()
    {
        $url = app(Client::class)->getUrl();

        $this->assertEquals(Client::DEMO_URL, $url, 'The URL should be '.Client::DEMO_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_production_url()
    {
        app('config')->set('services.viva.environment', 'production');

        $url = app(Client::class)->getUrl();

        $this->assertEquals(Client::PRODUCTION_URL, $url, 'The URL should be '.Client::PRODUCTION_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_throws_an_exception_when_the_environment_is_invalid()
    {
        $this->expectException(InvalidArgumentException::class);

        app('config')->set('services.viva.environment', '');

        $url = app(Client::class)->getUrl();
    }

    /**
     * @test
     */
    public function it_doesnt_use_tlsv1_for_nss()
    {
        $client = app(Client::class);

        $curl = $client->getClient()->getConfig('curl');

        if (preg_match('/NSS/', curl_version()['ssl_version'])) {
            $this->assertEmpty($curl);
        } else {
            $this->assertEquals([CURLOPT_SSL_CIPHER_LIST => 'TLSv1'], $curl);
        }
    }
}
