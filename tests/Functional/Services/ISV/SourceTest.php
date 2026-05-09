<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateSource;
use Sebdesign\VivaPayments\Services\ISV\Source;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Source::class)]
#[CoversClass(CreateSource::class)]
class SourceTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_a_source(): void
    {
        $this->expectException(GuzzleException::class);
        $this->expectExceptionCode(404);

        /** @var array<string,string> $config */
        $config = config('services.viva');

        Viva::withBasicAuthCredentials(
            $config['isv_partner_id'].':'.$config['merchant_id'],
            $config['isv_partner_api_key'],
        );

        Viva::isv()->sources()->create(new CreateSource(
            domain: 'www.example.com',
            isSecure: true,
            name: 'API test',
            pathFail: 'fail',
            pathSuccess: 'success',
            sourceCode: '1234',
            walletId: 100000000000,
            isPhysical: false,
        ));
    }
}
