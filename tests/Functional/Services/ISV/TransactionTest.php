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
            strval(config('services.viva.isv_client_id')),
            strval(config('services.viva.isv_client_secret')),
        );

        try {
            Viva::isv()->transactions()->retrieve(fake()->uuid());
            $this->fail();
        } catch (RequestException $e) {
            self::assertEquals(404, $e->getCode());
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
            strval(config('services.viva.isv_partner_id')).':'.strval(config('services.viva.merchant_id')),
            strval(config('services.viva.isv_partner_api_key')),
        );

        Viva::isv()->transactions()->createRecurring(
            fake()->uuid(),
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
