<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Illuminate\Support\Carbon;
use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Test\TestCase;

class CardTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_creates_a_token()
    {
        // arrange

        $expirationDate = Carbon::parse('next year');

        // act

        $token = app(Card::class)->token('Customer name', '4111 1111 1111 1111', 111, $expirationDate->month, $expirationDate->year);

        // assert

        $this->assertIsString($token);
    }

    /**
     * @test
     * @group functional
     */
    public function it_checks_for_installments()
    {
        $installments = app(Card::class)->installments('4111 1111 1111 1111');

        $this->assertIsInt($installments);
    }
}
