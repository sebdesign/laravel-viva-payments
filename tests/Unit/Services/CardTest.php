<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Services\Card;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(Card::class)]
class CardTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_creates_a_card_token(): void
    {
        $this->mockJsonResponses(['token' => 'ct_480c964156d949c19abe1b1061b21108']);
        $this->mockRequests();

        $this->client->withToken('test', Carbon::now()->addHour());

        $token = $this->client->cards()->createToken('6cffe5bf-909c-4d69-b6dc-2bef1a6202f7');

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertJsonBody('transactionId', '6cffe5bf-909c-4d69-b6dc-2bef1a6202f7', $request);
        $this->assertEquals('ct_480c964156d949c19abe1b1061b21108', $token, 'The card token should be ct_480c964156d949c19abe1b1061b21108');
    }
}
