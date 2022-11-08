<?php

namespace Sebdesign\VivaPayments\Enums;

enum TransactionType: int
{
    case CardCapture = 0;
    case CardPreAuth = 1;
    case CardRefund = 4;
    case CardCharge = 5;
    case CardChargeInstallments = 6;
    case CardVoid = 7;
    case CardOriginalCredit = 8;
    case VivaWalletCharge = 9;
    case VivaWalletRefund = 11;
    case CardRefundClaimed = 13;
    case Dias = 15;
    case Cash = 16;
    case CashRefund = 17;
    case CardRefundInstallments = 18;
    case CardPayout = 19;
    case AlipayCharge = 20;
    case AlipayRefund = 21;
    case CardManualCashDisbursement = 22;
    case IdealCharge = 23;
    case IdealRefund = 24;
    case P24Charge = 25;
    case P24Refund = 26;
    case BlikCharge = 27;
    case BlikRefund = 28;
    case PayUCharge = 29;
    case PayURefund = 30;
    case CardWithdrawal = 31;
    case MultibancoCharge = 32;
    case GiropayCharge = 34;
    case GiropayRefund = 35;
    case SofortCharge = 36;
    case SofortRefund = 37;
    case EPSCharge = 38;
    case EPSRefund = 39;
    case WeChatPayCharge = 40;
    case WeChatPayRefund = 41;
    case BitPayCharge = 42;
    case SepaDirectDebitCharge = 44;
    case SepaDirectDebitRefund = 45;
    case PayPalCharge = 48;
    case PayPalRefund = 49;
    case TrustlyCharge = 50;
    case TrustlyRefund = 51;
    case KlarnaCharge = 52;
    case KlarnaRefund = 53;
    case PayconiqCharge = 58;
    case PayconiqRefund = 59;
    case IrisCharge = 60;
    case IrisRefund = 61;
    case OnlineBankingCharge = 62;
    case OnlineBankingRefund = 63;
    case TbiBankCharge = 66;
    case TbiBankRefund = 67;
}
