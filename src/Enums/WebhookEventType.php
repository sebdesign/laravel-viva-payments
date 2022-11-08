<?php

namespace Sebdesign\VivaPayments\Enums;

enum WebhookEventType: int
{
    /** A customer’s payment has been successful */
    case TransactionPaymentCreated = 1796;

    /** A customer’s payment failed (but the customer may retry and the customer’s payment may - eventually - be successful) */
    case TransactionFailed = 1798;

    /** A commission payment has been withdrawn from your account by Viva Wallet */
    case TransactionPriceCalculated = 1799;

    /** A customer refund has been successfully actioned */
    case TransactionReversalCreated = 1797;

    /** A wallet account balance change */
    case AccountTransactionCreated = 2054;

    /** A bank transfer to an external IBAN has been created but not executed yet (the money has not yet been transferred from your wallet) */
    case CommandBankTransferCreated = 768;

    /**
     * A bank transfer to an external IBAN has been executed.
     *
     * In case of instant bank account transfer, money has been transferred immediately from your wallet - which is linked with your IBAN - to the external IBAN.
     * In case of non-instant bank account transfer, money has been transferred from your wallet - which is linked with your IBAN - but not necessarily received yet to the external IBAN
     */
    case CommandBankTransferExecuted = 769;

    /** A marketplace obligation (e.g. refund request) has been successfully sent to a marketplace seller */
    case ObligationCreated = 5632;

    /** A marketplace obligation (e.g. refund request) has been successfully paid by a marketplace seller */
    case ObligationCaptured = 5633;

    /** The requested sale transactions are available to download */
    case SaleTransactions = 0;
}
