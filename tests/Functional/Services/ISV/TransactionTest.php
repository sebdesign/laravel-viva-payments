<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
use Sebdesign\VivaPayments\Services\ISV;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(ISV::class)]
#[CoversClass(ISV\Transaction::class)]
#[UsesClass(CreateRecurringTransaction::class)]
class TransactionTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_cannot_retrieve_an_isv_transaction_that_does_not_exist(): void
    {
        Viva::withOAuthCredentials(
            strval(env('VIVA_ISV_CLIENT_ID')),
            strval(env('VIVA_ISV_CLIENT_SECRET')),
        );

        try {
            Viva::isv()->transactions()->retrieve('c90d4902-6245-449f-b2b0-51d99cd09cfe');
            $this->fail();
        } catch (RequestException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     *
     * @see https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Recurring-Payments/paths/~1api~1transactions~1{id}/post
     */
    #[Test]
    public function it_cannot_create_a_recurring_transaction_that_does_not_exist(): void
    {
        $this->expectException(VivaException::class);
        $this->expectExceptionCode(404);

        Viva::withBasicAuthCredentials(
            strval(env('VIVA_ISV_PARTNER_ID')).':'.strval(env('VIVA_MERCHANT_ID')),
            strval(env('VIVA_ISV_PARTNER_API_KEY')),
        );

        Viva::isv()->transactions()->createRecurring(
            '252b950e-27f2-4300-ada1-4dedd7c17904',
            new CreateRecurringTransaction(
                amount: 100,
                isvAmount: 1,
                customerTrns: 'A description of products / services that is displayed to the customer',
                merchantTrns: 'Your merchant reference',
                sourceCode: '6054',
            )
        );
    }
}
