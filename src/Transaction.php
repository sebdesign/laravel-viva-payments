<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;

class Transaction
{
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

    public function __construct(protected Client $client)
    {
    }

    /**
     * Retrieve transaction.
     *
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions/paths/~1checkout~1v2~1transactions~1{transactionId}/get
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     */
    public function retrieve(string $transactionId, array $guzzleOptions = []): Responses\Transaction
    {
        /** @phpstan-var TransactionArray */
        $response = $this->client->get(
            $this->client->getApiUrl()->withPath("/checkout/v2/transactions/{$transactionId}"),
            array_merge_recursive(
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions,
            )
        );

        return Responses\Transaction::create($response);
    }

    /**
     * Create a recurring transaction.
     *
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions-(Deprecated)/paths/~1api~1transactions~1{transaction_id}/post
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     */
    public function createRecurring(
        string $transactionId,
        CreateRecurringTransaction $transaction,
        array $guzzleOptions = []
    ): Responses\RecurringTransaction {
        /** @phpstan-var RecurringTransactionArray */
        $response = $this->client->post(
            $this->client->getUrl()->withPath("/api/transactions/{$transactionId}"),
            array_merge_recursive(
                [RequestOptions::JSON => $transaction],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return Responses\RecurringTransaction::create($response);
    }
}
