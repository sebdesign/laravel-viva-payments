<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Source;
use Sebdesign\VivaPayments\Test\TestCase;

class SourceTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_adds_a_payment_source()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $source = new Source($this->client);

        $source->create('Site 1', 'site1', 'https://www.domain.com', 'order/failure', 'order/success');
        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertJsonBody('name', 'Site 1', $request);
        $this->assertJsonBody('sourceCode', 'site1', $request);
        $this->assertJsonBody('domain', 'www.domain.com', $request);
        $this->assertJsonBody('isSecure', true, $request);
        $this->assertJsonBody('pathFail', 'order/failure', $request);
        $this->assertJsonBody('pathSuccess', 'order/success', $request);
    }
}
