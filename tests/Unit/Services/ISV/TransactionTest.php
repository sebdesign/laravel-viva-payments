<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
use Sebdesign\VivaPayments\Responses\RecurringTransaction;
use Sebdesign\VivaPayments\Services\ISV;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

#[CoversClass(Client::class)]
#[CoversClass(ISV::class)]
#[CoversClass(ISV\Transaction::class)]
#[CoversClass(CreateRecurringTransaction::class)]
#[CoversClass(RecurringTransaction::class)]
class TransactionTest extends TestCase
{
    /**
     * @throws GuzzleException
     * @throws VivaException
     */
    #[Test]
    public function it_retrieves_an_isv_transaction_by_transaction_id(): void
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

        $transaction = $this->client->withToken('test', Carbon::now()->addHour())->isv()
            ->transactions()->retrieve('c90d4902-6245-449f-b2b0-51d99cd09cfe');

        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertPath('/checkout/v2/isv/transactions/c90d4902-6245-449f-b2b0-51d99cd09cfe', $request);
        $this->assertQuery('merchantId', strval(env('VIVA_MERCHANT_ID')), $request);
        $this->assertEquals('someone@example.com', $transaction->email);
        $this->assertEquals(30.00, $transaction->amount);
        $this->assertEquals(6962462482972601, $transaction->orderCode);
        $this->assertEquals('F', $transaction->statusId->value);
        $this->assertEquals('George Seferis', $transaction->fullName);
        $this->assertEquals('2021-12-06T14:32:10.32+02:00', $transaction->insDate);
        $this->assertEquals('523929XXXXXX0168', $transaction->cardNumber);
        $this->assertEquals('978', $transaction->currencyCode);
        $this->assertEquals('Short description of items/services purchased to display to your customer', $transaction->customerTrns);
        $this->assertEquals('Short description of items/services purchased by customer', $transaction->merchantTrns);
        $this->assertEquals('9521B4209B611B11E080964E09640F4EB3C3AA18', $transaction->cardUniqueReference);
        $this->assertEquals(5, $transaction->transactionTypeId->value);
        $this->assertEquals(false, $transaction->recurringSupport);
        $this->assertEquals(0, $transaction->totalInstallments);
        $this->assertEquals(null, $transaction->cardCountryCode);
        $this->assertEquals(null, $transaction->cardIssuingBank);
        $this->assertEquals(0, $transaction->currentInstallment);
        $this->assertEquals(1, $transaction->cardTypeId);
        $this->assertEquals('NET_VISA', $transaction->bankId);
        $this->assertFalse($transaction->switching);
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

        $response = $this->client->isv()->transactions()->createRecurring(
            '14c59e93-f8e4-4f5c-8a63-60ae8f8807d1',
            new CreateRecurringTransaction(
                amount: 100,
                isvAmount: 1,
                customerTrns: 'A description of products / services that is displayed to the customer',
                merchantTrns: 'Your merchant reference',
                sourceCode: '4929333',
                resellerSourceCode: '1565',
            )
        );

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertPath('/api/transactions/14c59e93-f8e4-4f5c-8a63-60ae8f8807d1', $request);
        $this->assertJsonBody('amount', 100, $request);
        $this->assertJsonBody('isvAmount', 1, $request);
        $this->assertJsonBody('customerTrns', 'A description of products / services that is displayed to the customer', $request);
        $this->assertJsonBody('merchantTrns', 'Your merchant reference', $request);
        $this->assertJsonBody('sourceCode', '4929333', $request);
        $this->assertJsonBody('resellerSourceCode', '1565', $request);
        $this->assertEquals(null, $response->Emv);
        $this->assertEquals(1.00, $response->Amount);
        $this->assertEquals('F', $response->StatusId->value);
        $this->assertEquals(5, $response->TransactionTypeId->value);
        $this->assertEquals(null, $response->RedirectUrl);
        $this->assertEquals('826', $response->CurrencyCode);
        $this->assertEquals('14c59e93-f8e4-4f5c-8a63-60ae8f8807d1', $response->TransactionId);
        $this->assertEquals(838982, $response->ReferenceNumber);
        $this->assertEquals('838982', $response->AuthorizationId);
        $this->assertEquals('109012838982', $response->RetrievalReferenceNumber);
        $this->assertEquals(null, $response->Loyalty);
        $this->assertEquals(2, $response->ThreeDSecureStatusId);
        $this->assertEquals(0, $response->ErrorCode);
        $this->assertEquals(null, $response->ErrorText);
        $this->assertEquals('2021-03-31T15:52:27.2029634+03:00', $response->TimeStamp);
        $this->assertEquals(null, $response->CorrelationId);
        $this->assertEquals(0, $response->EventId);
        $this->assertEquals(true, $response->Success);
    }
}
