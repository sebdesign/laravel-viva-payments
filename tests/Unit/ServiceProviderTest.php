<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

/** @covers \Sebdesign\VivaPayments\VivaPaymentsServiceProvider */
class ServiceProviderTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_is_deferred(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertTrue($provider->isDeferred());
    }

    /**
     * @test
     * @group unit
     */
    public function it_merges_the_configuration(): void
    {
        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->app?->make('config');
        $config = $config->get('services.viva');

        $this->assertIsArray($config);
        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('api_key', $config);
        $this->assertArrayHasKey('merchant_id', $config);
        $this->assertArrayHasKey('environment', $config);
    }

    /**
     * @test
     * @group unit
     */
    public function it_provides_the_client(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertContains(Client::class, $provider->provides());
    }

    /**
     * @test
     * @group unit
     */
    public function it_resolves_the_client_as_a_singleton(): void
    {
        $client = $this->app?->make(Client::class);

        $this->assertInstanceof(Client::class, $client);
        $this->assertTrue($this->app?->isShared(Client::class));
    }

    /**
     * @test
     * @group unit
     */
    public function it_resolves_the_oauth(): void
    {
        $oauth = $this->app?->make(OAuth::class);

        $this->assertInstanceof(OAuth::class, $oauth);
    }

    /**
     * @test
     */
    public function it_doesnt_use_tlsv1_for_nss(): void
    {
        $client = app(Client::class);

        $curl = $client->client->getConfig('curl');

        $version = curl_version();

        if (
            is_array($version) &&
            isset($version['ssl_version']) &&
            str_contains($version['ssl_version'], 'NSS')
        ) {
            $this->assertEmpty($curl);
        } else {
            $this->assertEquals([CURLOPT_SSL_CIPHER_LIST => 'TLSv1.2'], $curl);
        }
    }
}
