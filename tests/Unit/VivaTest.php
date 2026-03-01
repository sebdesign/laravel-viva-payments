<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;

#[CoversClass(Viva::class)]
class VivaTest extends TestCase
{
    #[Test]
    public function it_proxies_the_client(): void
    {
        $viva = Viva::getFacadeRoot();

        self::assertInstanceOf(Client::class, $viva);
    }
}
