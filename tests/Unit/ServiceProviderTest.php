<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Services\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

#[CoversClass(VivaPaymentsServiceProvider::class)]
final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_is_deferred(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        self::assertTrue($provider->isDeferred());
    }

    #[Test]
    public function it_merges_the_configuration(): void
    {
        $config = Config::get('services.viva');

        self::assertIsArray($config);
        self::assertNotEmpty($config);
        self::assertArrayHasKey('api_key', $config);
        self::assertArrayHasKey('merchant_id', $config);
        self::assertArrayHasKey('environment', $config);
    }

    #[Test]
    public function it_provides_the_client(): void
    {
        /** @var VivaPaymentsServiceProvider */
        $provider = $this->app?->getProvider(VivaPaymentsServiceProvider::class);

        self::assertContains(Client::class, $provider->provides());
    }

    #[Test]
    public function it_resolves_the_client_as_a_singleton(): void
    {
        $client = $this->app?->make(Client::class);

        self::assertInstanceOf(Client::class, $client);
        self::assertTrue($this->app?->isShared(Client::class));
    }

    #[Test]
    public function it_resolves_the_oauth(): void
    {
        $oauth = $this->app?->make(OAuth::class);

        self::assertInstanceOf(OAuth::class, $oauth);
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
            is_string($version['ssl_version']) &&
            str_contains($version['ssl_version'], 'NSS')
        ) {
            self::assertEmpty($curl);
        } else {
            self::assertEquals([CURLOPT_SSL_CIPHER_LIST => 'TLSv1.2'], $curl);
        }
    }
}
