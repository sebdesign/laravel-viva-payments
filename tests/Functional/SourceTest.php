<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Illuminate\Support\Str;
use Sebdesign\VivaPayments\Source;
use Sebdesign\VivaPayments\Test\TestCase;

class SourceTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_adds_a_payment_source()
    {
        $response = app(Source::class)->create('Site 1', Str::random(), 'https://www.domain.com', 'order/failure', 'order/success');

        $this->assertNull($response);
    }
}
