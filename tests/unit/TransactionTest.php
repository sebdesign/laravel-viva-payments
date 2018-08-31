<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use DateTime;
use Illuminate\Support\Carbon;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Transaction;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_a_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $parameters = [
            'OrderCode'     => 175936509216,
            'SourceCode'    => 'Default',
            'Installments'  => 36,
            'CreditCard'    => [
                'Token'     => 'foo',
            ],
        ];

        $response = $transaction->create($parameters);
        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertQuery('key', $this->app['config']->get('services.viva.public_key'), $request);
        $this->assertBody('OrderCode', '175936509216', $request);
        $this->assertBody('SourceCode', 'Default', $request);
        $this->assertBody('Installments', '36', $request);
        $this->assertBody('CreditCard', ['Token' => 'foo'], $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_creates_a_recurring_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $response = $transaction->createRecurring('252b950e-27f2-4300-ada1-4dedd7c17904', 30, [
            'MerchantTrns' => 'Your reference',
        ]);

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertBody('Amount', '30', $request);
        $this->assertBody('MerchantTrns', 'Your reference', $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_a_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $response = $transaction->cancel('252b950e-27f2-4300-ada1-4dedd7c17904', 30, 'Customer name');
        $request = $this->getLastRequest();

        $this->assertMethod('DELETE', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertQuery('Amount', '30', $request);
        $this->assertQuery('ActionUser', 'Customer name', $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_id()
    {
        $this->mockJsonResponses([
            ['Transactions' => [
                ['foo' => 'bar'],
            ]],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->get('252b950e-27f2-4300-ada1-4dedd7c17904');
        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertEquals([(object) ['foo' => 'bar']], $transactions);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_order_code()
    {
        $this->mockJsonResponses([
            ['Transactions' => [
                ['foo' => 'bar'],
            ]],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->getByOrder(175936509216);

        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertQuery('ordercode', '175936509216', $request);
        $this->assertEquals([(object) ['foo' => 'bar']], $transactions);
    }

    /**
     * @test
     * @group unit
     * @dataProvider dates
     */
    public function it_gets_transactions_by_date($date)
    {
        $this->mockJsonResponses([
            ['Transactions' => [
                ['foo' => 'bar'],
            ]],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->getByDate($date);

        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertQuery('date', '2016-03-12', $request);
        $this->assertEquals([(object) ['foo' => 'bar']], $transactions);
    }

    public function dates() : array
    {
        return [
            'string' => ['2016-03-12'],
            DateTime::class => [new DateTime('2016-03-12')],
            Carbon::class => [new Carbon('2016-03-12')],
        ];
    }

    /**
     * @test
     * @group unit
     * @dataProvider clearanceDates
     */
    public function it_gets_transactions_by_clearance_date($date)
    {
        $this->mockJsonResponses([
            ['Transactions' => [
                ['foo' => 'bar'],
            ]],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->getByClearanceDate($date);

        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertQuery('clearancedate', '2016-03-12', $request);
        $this->assertEquals([(object) ['foo' => 'bar']], $transactions);
    }

    public function clearanceDates() : array
    {
        return [
            'string' => ['2016-03-12'],
            DateTime::class => [new DateTime('2016-03-12')],
            Carbon::class => [new Carbon('2016-03-12')],
        ];
    }
}
