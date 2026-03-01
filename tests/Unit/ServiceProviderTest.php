<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Services\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

#[CoversClass(VivaPaymentsServiceProvider::class)]
class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_is_deferred(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertTrue($provider->isDeferred());
    }

    #[Test]
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

    #[Test]
    public function it_provides_the_client(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        $this->assertContains(Client::class, $provider->provides());
    }

    #[Test]
    public function it_resolves_the_client_as_a_singleton(): void
    {
        $client = $this->app?->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertTrue($this->app?->isShared(Client::class));
    }

    #[Test]
    public function it_resolves_the_oauth(): void
    {
        $oauth = $this->app?->make(OAuth::class);

        $this->assertInstanceOf(OAuth::class, $oauth);
    }

    #[Test]
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
