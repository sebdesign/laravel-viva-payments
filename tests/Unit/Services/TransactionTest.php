<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
use Sebdesign\VivaPayments\Responses\RecurringTransaction;
use Sebdesign\VivaPayments\Responses\Transaction;
use Sebdesign\VivaPayments\Services\Transaction as TransactionService;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(CreateRecurringTransaction::class)]
#[CoversClass(TransactionService::class)]
#[CoversClass(Transaction::class)]
#[CoversClass(RecurringTransaction::class)]
class TransactionTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_retrieves_a_transaction(): void
    {
        $this->mockJsonResponses([
            'email' => 'someone@example.com',
            'amount' => 30.00,
            'orderCode' => 6962462482972601,
            'statusId' => 'F',
            'fullName' => 'George Seferis',
            'insDate' => '2021-12-06T14:32:10.32+02:00',
            'cardNumber' => '523929XXXXXX0168',
            'currencyCode' => '978',
            'customerTrns' => 'Short description of items/services purchased to display to your customer',
            'merchantTrns' => 'Short description of items/services purchased by customer',
            'transactionTypeId' => 5,
            'recurringSupport' => false,
            'totalInstallments' => 0,
            'cardCountryCode' => null,
            'cardIssuingBank' => null,
            'currentInstallment' => 0,
            'cardUniqueReference' => '9521B4209B611B11E080964E09640F4EB3C3AA18',
            'cardTypeId' => 1,
            'bankId' => 'NET_VISA',
            'switching' => false,
        ]);
        $this->mockRequests();

        $transaction = $this->client->withToken('test', Carbon::now()->addHour())
            ->transactions()->retrieve('c90d4902-6245-449f-b2b0-51d99cd09cfe');

        $request = $this->getLastRequest();

        self::assertMethod('GET', $request);
        self::assertPath('/checkout/v2/transactions/c90d4902-6245-449f-b2b0-51d99cd09cfe', $request);
        self::assertEquals('someone@example.com', $transaction->email);
        self::assertEquals(30.00, $transaction->amount);
        self::assertEquals(6962462482972601, $transaction->orderCode);
        self::assertEquals('F', $transaction->statusId->value);
        self::assertEquals('George Seferis', $transaction->fullName);
        self::assertEquals('2021-12-06T14:32:10.32+02:00', $transaction->insDate);
        self::assertEquals('523929XXXXXX0168', $transaction->cardNumber);
        self::assertEquals('978', $transaction->currencyCode);
        self::assertEquals('Short description of items/services purchased to display to your customer', $transaction->customerTrns);
        self::assertEquals('Short description of items/services purchased by customer', $transaction->merchantTrns);
        self::assertEquals('9521B4209B611B11E080964E09640F4EB3C3AA18', $transaction->cardUniqueReference);
        self::assertEquals(5, $transaction->transactionTypeId->value);
        self::assertEquals(false, $transaction->recurringSupport);
        self::assertEquals(0, $transaction->totalInstallments);
        self::assertEquals(null, $transaction->cardCountryCode);
        self::assertEquals(null, $transaction->cardIssuingBank);
        self::assertEquals(0, $transaction->currentInstallment);
        self::assertEquals(1, $transaction->cardTypeId);
        self::assertEquals('NET_VISA', $transaction->bankId);
        self::assertFalse($transaction->switching);
    }

    /**
     * @throws GuzzleException
     * @throws VivaException
     *
     * @see https://developer.vivawallet.com/tutorials/payments/create-a-recurring-payment/#via-the-api
     */
    #[Test]
    public function it_creates_a_recurring_transaction(): void
    {
        $this->mockJsonResponses([
            'Emv' => null,
            'Amount' => 1.00,
            'StatusId' => 'F',
            'RedirectUrl' => null,
            'CurrencyCode' => '826',
            'TransactionId' => '14c59e93-f8e4-4f5c-8a63-60ae8f8807d1',
            'TransactionTypeId' => 5,
            'ReferenceNumber' => 838982,
            'AuthorizationId' => '838982',
            'RetrievalReferenceNumber' => '109012838982',
            'Loyalty' => null,
            'ThreeDSecureStatusId' => 2,
            'ErrorCode' => 0,
            'ErrorText' => null,
            'TimeStamp' => '2021-03-31T15:52:27.2029634+03:00',
            'CorrelationId' => null,
            'EventId' => 0,
            'Success' => true,
        ]);
        $this->mockRequests();

        $response = $this->client->transactions()->createRecurring(
            '14c59e93-f8e4-4f5c-8a63-60ae8f8807d1',
            new CreateRecurringTransaction(
                amount: 100,
                installments: 1,
                customerTrns: 'A description of products / services that is displayed to the customer',
                merchantTrns: 'Your merchant reference',
                sourceCode: '6054',
                tipAmount: 0,
            )
        );

        $request = $this->getLastRequest();

        self::assertMethod('POST', $request);
        self::assertPath('/api/transactions/14c59e93-f8e4-4f5c-8a63-60ae8f8807d1', $request);
        self::assertJsonBody('amount', 100, $request);
        self::assertJsonBody('installments', 1, $request);
        self::assertJsonBody('customerTrns', 'A description of products / services that is displayed to the customer', $request);
        self::assertJsonBody('merchantTrns', 'Your merchant reference', $request);
        self::assertJsonBody('sourceCode', '6054', $request);
        self::assertJsonBody('tipAmount', 0, $request);
        self::assertEquals(null, $response->Emv);
        self::assertEquals(1.00, $response->Amount);
        self::assertEquals('F', $response->StatusId->value);
        self::assertEquals(5, $response->TransactionTypeId->value);
        self::assertEquals(null, $response->RedirectUrl);
        self::assertEquals('826', $response->CurrencyCode);
        self::assertEquals('14c59e93-f8e4-4f5c-8a63-60ae8f8807d1', $response->TransactionId);
        self::assertEquals(838982, $response->ReferenceNumber);
        self::assertEquals('838982', $response->AuthorizationId);
        self::assertEquals('109012838982', $response->RetrievalReferenceNumber);
        self::assertEquals(null, $response->Loyalty);
        self::assertEquals(2, $response->ThreeDSecureStatusId);
        self::assertEquals(0, $response->ErrorCode);
        self::assertEquals(null, $response->ErrorText);
        self::assertEquals('2021-03-31T15:52:27.2029634+03:00', $response->TimeStamp);
        self::assertEquals(null, $response->CorrelationId);
        self::assertEquals(0, $response->EventId);
        self::assertEquals(true, $response->Success);
    }
}
