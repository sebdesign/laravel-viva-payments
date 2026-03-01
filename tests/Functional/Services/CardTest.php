<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Services\Card;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Card::class)]
final class CardTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_cannot_create_a_card_token_for_a_transaction_that_does_not_exist(): void
    {
        try {
            Viva::cards()->createToken('6cffe5bf-909c-4d69-b6dc-2bef1a6202f7');

            $this->fail();
        } catch (ClientException $e) {
            self::assertEquals(403, $e->getCode());
        }
    }
}
