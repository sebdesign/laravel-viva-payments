<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Illuminate\Support\Carbon;
use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\Transaction;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function createTransaction()
    {
        $orderCode = $this->getOrderCode();
        $token = $this->getToken();
        $installments = $this->getInstallments();

        $original = app(Transaction::class)->create([
            'orderCode'       => $orderCode,
            'sourceCode'      => env('VIVA_SOURCE_CODE'),
            'installments'    => $installments,
            'allowsRecurring' => true,
            'creditCard'      => [
                'token'       => $token,
            ],
        ]);

        $this->assertEquals(Transaction::COMPLETED, $original->StatusId, 'The transaction was not completed.');
        $this->assertEquals(15, $original->Amount);

        return $original;
    }

    /**
     * @test
     * @group functional
     * @depends createTransaction
     */
    public function createRecurringTransaction($original)
    {
        $installments = $this->getInstallments();

        $recurring = app(Transaction::class)->createRecurring($original->TransactionId, 1500, [
            'sourceCode'    => env('VIVA_SOURCE_CODE'),
            'installments'  => $installments,
        ]);

        $this->assertEquals(Transaction::COMPLETED, $recurring->StatusId, 'The transaction was not completed.');
        $this->assertEquals(15, $recurring->Amount);
    }

    /**
     * @test
     * @group functional
     * @depends createTransaction
     */
    public function getById($original)
    {
        $transactions = app(Transaction::class)->get($original->TransactionId);

        $this->assertNotEmpty($transactions);
        $this->assertCount(1, $transactions, 'There should be 1 transaction.');
        $this->assertEquals(Transaction::COMPLETED, $transactions[0]->StatusId, 'The transaction was not completed.');
        $this->assertEquals($original->TransactionId, $transactions[0]->TransactionId, "The transaction ID should be {$original->TransactionId}.");

        return $transactions[0];
    }

    /**
     * @test
     * @group functional
     * @depends getById
     */
    public function getByOrderCode($original)
    {
        $orderCode = $original->Order->OrderCode;

        $transactions = app(Transaction::class)->getByOrder($orderCode);

        $this->assertNotEmpty($transactions);

        foreach ($transactions as $key => $trns) {
            $this->assertEquals($orderCode, $trns->Order->OrderCode, "Transaction #{$key} should have order code {$orderCode}");
        }
    }

    /**
     * @test
     * @group functional
     * @depends getById
     */
    public function getByDate($original)
    {
        $date = Carbon::parse($original->InsDate);

        $transactions = app(Transaction::class)->getByDate($date);

        $this->assertNotEmpty($transactions);

        foreach ($transactions as $transaction) {
            $this->assertTrue(Carbon::parse($transaction->InsDate)->isSameDay($date));
        }
    }

    /**
     * @test
     * @group functional
     * @depends getById
     */
    public function getByClearanceDate($original)
    {
        $this->markTestSkipped('Clearance date is null.');

        $date = Carbon::parse($original->InsDate);

        $transactions = app(Transaction::class)->getByClearanceDate($date);

        $this->assertNotEmpty($transactions);

        foreach ($transactions as $key => $trns) {
            $this->assertTrue(Carbon::parse($trns->ClearanceDate)->isSameDay($date));
        }
    }

    /**
     * @test
     * @group functional
     * @depends createTransaction
     */
    public function cancelTransaction($original)
    {
        $transaction = app(Transaction::class);

        $response = $transaction->cancel($original->TransactionId, 1500);

        $this->assertEquals(Transaction::COMPLETED, $response->StatusId, 'The cancel transaction was not completed.');
        $this->assertEquals(15, $response->Amount);

        $transactions = $transaction->get($original->TransactionId);

        $this->assertNotEmpty($transactions);
        $this->assertCount(1, $transactions, 'There should be 1 transaction.');
        $this->assertEquals(Transaction::CANCELED, $transactions[0]->StatusId, 'The original transaction should be canceled.');
        $this->assertEquals(15, $transactions[0]->Amount);
    }

    protected function getOrderCode()
    {
        return app(Order::class)->create(1500, [
            'customerTrns' => 'Test Transaction',
            'sourceCode' => env('VIVA_SOURCE_CODE'),
            'allowRecurring' => true,
        ]);
    }

    protected function getToken()
    {
        $expirationDate = Carbon::parse('next year');

        return app(Card::class)->token('Customer name', '4111 1111 1111 1111', 111, $expirationDate->month, $expirationDate->year);
    }

    protected function getInstallments()
    {
        return app(Card::class)->installments('4111 1111 1111 1111');
    }
}
