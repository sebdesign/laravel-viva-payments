<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
use Sebdesign\VivaPayments\Services\Transaction;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Transaction::class)]
#[CoversClass(CreateRecurringTransaction::class)]
class TransactionTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_cannot_retrieve_a_transaction_that_does_not_exist(): void
    {
        try {
            Viva::transactions()->retrieve(fake()->uuid());
            self::fail();
        } catch (RequestException $e) {
            self::assertEquals(404, $e->getCode());
        }
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     *
     * @see https://developer.vivawallet.com/tutorials/payments/create-a-recurring-payment/#via-the-api
     */
    #[Test]
    public function it_cannot_create_a_recurring_transaction_that_does_not_exist(): void
    {
        $this->expectException(VivaException::class);
        $this->expectExceptionCode(404);

        Viva::transactions()->createRecurring(
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
