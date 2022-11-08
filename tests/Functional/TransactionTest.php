<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use GuzzleHttp\Exception\RequestException;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Transaction;
use Sebdesign\VivaPayments\VivaException;

/** @covers \Sebdesign\VivaPayments\Transaction */
class TransactionTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Responses\Transaction
     */
    public function it_cannot_retrieve_a_transaction_that_does_not_exist(): void
    {
        /** @var Transaction */
        $transaction = $this->app?->make(Transaction::class);

        try {
            $transaction->retrieve('c90d4902-6245-449f-b2b0-51d99cd09cfe');
            $this->fail();
        } catch (RequestException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Requests\CreateRecurringTransaction
     * @covers \Sebdesign\VivaPayments\Responses\RecurringTransaction
     *
     * @see https://developer.vivawallet.com/tutorials/payments/create-a-recurring-payment/#via-the-api
     */
    public function it_cannot_create_a_recurring_transaction_that_does_not_exist(): void
    {
        /** @var Transaction */
        $transaction = $this->app?->make(Transaction::class);

        $this->expectException(VivaException::class);
        $this->expectExceptionCode(404);

        $transaction->createRecurring(
            '252b950e-27f2-4300-ada1-4dedd7c17904',
            new CreateRecurringTransaction(
                amount: 100,
                installments: 1,
                customerTrns: 'A description of products / services that is displayed to the customer',
                merchantTrns: 'Your merchant reference',
                sourceCode: '6054',
                tipAmount: 0,
            )
        );
    }
}
