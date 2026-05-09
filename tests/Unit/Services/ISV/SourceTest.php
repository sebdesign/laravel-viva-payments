<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreateSource;
use Sebdesign\VivaPayments\Services\ISV\Source;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
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
        $this->mockJsonResponses([]);
        $this->mockRequests();

        $this->client->withToken('test', Carbon::now()->addHour());

        $source = new Source($this->client);

        $source->create(new CreateSource(
            domain: 'www.example.com',
            isSecure: true,
            name: 'API test',
            pathFail: 'fail',
            pathSuccess: 'success',
            sourceCode: '1234',
            phone: '2102312111',
            address: 'Amarousiou Chalandriou 18-20',
            walletId: 100000000000,
            isPhysical: true,
            latitude: 38.0425,
            longitude: 23.8086,
            transactionDescriptor: 'my store coffee',
        ));

        $request = $this->getLastRequest();

        self::assertMethod('POST', $request);
        self::assertJsonBody('domain', 'www.example.com', $request);
        self::assertJsonBody('isSecure', true, $request);
        self::assertJsonBody('name', 'API test', $request);
        self::assertJsonBody('pathFail', 'fail', $request);
        self::assertJsonBody('pathSuccess', 'success', $request);
        self::assertJsonBody('sourceCode', '1234', $request);
        self::assertJsonBody('phone', '2102312111', $request);
        self::assertJsonBody('address', 'Amarousiou Chalandriou 18-20', $request);
        self::assertJsonBody('walletId', 100000000000, $request);
        self::assertJsonBody('isPhysical', true, $request);
        self::assertJsonBody('latitude', 38.0425, $request);
        self::assertJsonBody('longitude', 23.8086, $request);
        self::assertJsonBody('transactionDescriptor', 'my store coffee', $request);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_an_ecommerce_source(): void
    {
        $this->mockJsonResponses([]);
        $this->mockRequests();

        $this->client->withToken('test', Carbon::now()->addHour());

        $source = new Source($this->client);

        $source->create(CreateSource::ecommerce(
            domain: 'www.example.com',
            isSecure: true,
            name: 'API test',
            pathFail: 'fail',
            pathSuccess: 'success',
            sourceCode: '1234',
        ));

        $request = $this->getLastRequest();

        self::assertMethod('POST', $request);
        self::assertJsonBody('domain', 'www.example.com', $request);
        self::assertJsonBody('isSecure', true, $request);
        self::assertJsonBody('name', 'API test', $request);
        self::assertJsonBody('pathFail', 'fail', $request);
        self::assertJsonBody('pathSuccess', 'success', $request);
        self::assertJsonBody('sourceCode', '1234', $request);
        self::assertJsonBody('isPhysical', false, $request);
    }
}
