<?php

namespace Sebdesign\VivaPayments;

class Transaction
{
    const ENDPOINT = '/api/transactions/';

    /**
     * Transaction types.
     */

    // A Capture event of a preAuthorized transaction
    const CAPTURE_FROM_PREAUTH = 0;

    // Authorization hold
    const PREAUTH = 1;

    // Refund transaction
    const REFUND_CARD = 4;

    // Card payment transaction
    const CHARGE_CARD = 5;

    // A card payment that will be done with installments
    const CHARGE_CARD_WITH_INSTALLMENTS = 6;

    // A payment cancelation
    const VOID = 7;

    // A Wallet Payment
    const WALLET_CHARGE = 9;

    // A Refund of a Wallet Payment
    const WALLET_REFUND = 11;

    // Refund transaction for a claimed transaction
    const CLAIM_REFUND = 13;

    // Payment made through the DIAS system
    const DIAS_PAYMENT = 15;

    // Cash Payments, through the Viva Payments Authorised Resellers Network
    const CASH_PAYMENT = 16;

    // A Refunded installment
    const REFUND_INSTALLMENTS = 18;

    // Clearance of a transactions batch
    const CLEARANCE = 19;

    // Bank Transfer command from the merchant's wallet to their IBAN
    const BANK_TRANSFER = 24;

    /**
     * Transaction statuses.
     */

    // The transaction was not completed because of an error
    const ERROR = 'E';

    // The transaction is in progress
    const PROGRESS = 'A';

    // The cardholder has disputed the transaction with the issuing Bank
    const DISPUTED = 'M';

    // Dispute Awaiting Response
    const DISPUTE_AWAITING = 'MA';

    // Dispute in Progress
    const DISPUTE_IN_PROGRESS = 'MI';

    // A disputed transaction has been refunded (Dispute Lost)
    const DISPUTE_REFUNDED = 'ML';

    // Dispute Won
    const DISPUTE_WON = 'MW';

    // Suspected Dispute
    const DISPUTE_SUSPECTED = 'MS';

    // The transaction was cancelled by the merchant
    const CANCELED = 'X';

    // The transaction has been fully or partially refunded
    const REFUNDED = 'R';

    // The transaction has been completed successfully
    const COMPLETED = 'F';

    /**
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \Sebdesign\VivaPayments\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new transaction.
     *
     * @param  array   $parameters
     * @return \stdClass
     */
    public function create(array $parameters)
    {
        return $this->client->post(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => $parameters,
            \GuzzleHttp\RequestOptions::QUERY => [
                'key' => $this->getKey(),
            ],
        ]);
    }

    /**
     * Create a recurring transaction.
     *
     * @param  string   $id
     * @param  int      $amount
     * @param  array    $parameters
     * @return \stdClass
     */
    public function createRecurring(string $id, int $amount, array $parameters = [])
    {
        return $this->client->post(self::ENDPOINT.$id, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => array_merge(['Amount' => $amount], $parameters),
        ]);
    }

    /**
     * Get the transactions for an id.
     *
     * @param  string $id
     * @return array
     */
    public function get(string $id) : array
    {
        $response = $this->client->get(self::ENDPOINT.$id);

        return $response->Transactions;
    }

    /**
     * Get the transactions for an order code.
     *
     * @param  int $ordercode
     * @return array
     */
    public function getByOrder($ordercode) : array
    {
        $response = $this->client->get(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::QUERY => compact('ordercode'),
        ]);

        return $response->Transactions;
    }

    /**
     * Get the transactions that occured on a given date.
     *
     * @param  \DateTimeInterface|string $date
     * @return array
     */
    public function getByDate($date) : array
    {
        $date = $this->formatDate($date);

        $response = $this->client->get(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::QUERY => compact('date'),
        ]);

        return $response->Transactions;
    }

    /**
     * Get the transactions that were cleared on a given date.
     *
     * @param  \DateTimeInterface|string $clearancedate
     * @return array
     */
    public function getByClearanceDate($clearancedate) : array
    {
        $clearancedate = $this->formatDate($clearancedate);

        $response = $this->client->get(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::QUERY => compact('clearancedate'),
        ]);

        return $response->Transactions;
    }

    /**
     * Format a date object to string.
     *
     * @param  \DateTimeInterface|string $date
     * @return string
     */
    protected function formatDate($date) : string
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        return $date;
    }

    /**
     * Cancel or refund a payment.
     *
     * @param  string       $id
     * @param  int          $amount
     * @param  string|null  $actionUser
     * @return \stdClass
     */
    public function cancel(string $id, int $amount, $actionUser = null)
    {
        $query = ['Amount' => $amount];
        $actionUser = $actionUser ? ['ActionUser' => $actionUser] : [];

        return $this->client->delete(self::ENDPOINT.$id, [
            \GuzzleHttp\RequestOptions::QUERY => array_merge($query, $actionUser),
        ]);
    }

    /**
     * Get the public key.
     *
     * @return string
     */
    protected function getKey() : string
    {
        return config('services.viva.public_key');
    }
}
