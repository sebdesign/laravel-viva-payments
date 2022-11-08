<?php

namespace Sebdesign\VivaPayments\Enums;

/**
 * @see https://developer.vivawallet.com/integration-reference/response-codes/#statusid-parameter
 */
enum TransactionStatus: string
{
    /** The transaction has been completed successfully (PAYMENT SUCCESSFUL) */
    case PaymentSuccessful = 'F';

    /** The transaction is in progress (PAYMENT PENDING) */
    case PaymentPending = 'A';

    /**
     * The transaction has been captured
     * (the C status refers to the original pre-auth transaction which has now been captured;
     * the capture will be a separate transaction with status F)
     */
    case Captured = 'C';

    /** The transaction was not completed successfully (PAYMENT UNSUCCESSFUL) */
    case Error = 'E';

    /** The transaction has been fully or partially refunded */
    case Refunded = 'R';

    /** The transaction was cancelled by the merchant */
    case Cancelled = 'X';

    /** The cardholder has disputed the transaction with the issuing Bank */
    case Disputed = 'M';

    /** Dispute Awaiting Response */
    case DisputeAwaiting = 'MA';

    /** Dispute in Progress */
    case DisputeInProgress = 'MI';

    /** A disputed transaction has been refunded (Dispute Lost) */
    case DisputeLost = 'ML';

    /** Dispute Won */
    case DisputeWon = 'MW';

    /** Suspected Dispute */
    case DisputeSuspected = 'MS';
}
