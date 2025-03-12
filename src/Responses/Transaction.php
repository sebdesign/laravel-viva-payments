<?php

namespace Sebdesign\VivaPayments\Responses;

use Sebdesign\VivaPayments\Enums\TransactionStatus;
use Sebdesign\VivaPayments\Enums\TransactionType;
use Spatie\LaravelData\Data;

class Transaction extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $bankId,
        public readonly float $amount,
        public readonly bool $switching,
        public readonly string $orderCode,
        public readonly TransactionStatus $statusId,
        public readonly string $fullName,
        public readonly string $insDate,
        public readonly ?string $cardNumber,
        public readonly string $currencyCode,
        public readonly string $customerTrns,
        public readonly ?string $merchantTrns,
        public readonly TransactionType $transactionTypeId,
        public readonly bool $recurringSupport,
        public readonly int $totalInstallments,
        public readonly ?string $cardCountryCode,
        public readonly ?string $cardIssuingBank,
        public readonly int $currentInstallment,
        public readonly ?string $cardUniqueReference,
        public readonly ?int $cardTypeId,
        public readonly ?int $digitalWalletId = null,
    ) {}
}
