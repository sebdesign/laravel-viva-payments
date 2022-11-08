<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Facades\SmartCheckout;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Transaction;
use Sebdesign\VivaPayments\Webhook;

/** @covers \Sebdesign\VivaPayments\Facades\SmartCheckout */
class SmartCheckoutTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_cards_object(): void
    {
        $cards = SmartCheckout::cards();

        $this->assertInstanceOf(Card::class, $cards);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_orders_object(): void
    {
        $orders = SmartCheckout::orders();

        $this->assertInstanceOf(Order::class, $orders);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_transactions_object(): void
    {
        $transactions = SmartCheckout::transactions();

        $this->assertInstanceOf(Transaction::class, $transactions);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_webhooks_object(): void
    {
        $webhooks = SmartCheckout::webhooks();

        $this->assertInstanceOf(Webhook::class, $webhooks);
    }
}
