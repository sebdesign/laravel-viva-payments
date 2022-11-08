<?php

namespace Sebdesign\VivaPayments\Responses;

use Sebdesign\VivaPayments\Enums\TransactionStatus;
use Sebdesign\VivaPayments\Enums\TransactionType;

class Transaction
{
    public function __construct(
        public readonly string $email,
        public readonly int $amount,
        public readonly string $orderCode,
        public readonly TransactionStatus $statusId,
        public readonly string $fullName,
        public readonly string $insDate,
        public readonly string $cardNumber,
        public readonly string $currencyCode,
        public readonly string $customerTrns,
        public readonly string $merchantTrns,
        public readonly TransactionType $transactionTypeId,
        public readonly bool $recurringSupport,
        public readonly int $totalInstallments,
        public readonly ?string $cardCountryCode,
        public readonly ?string $cardIssuingBank,
        public readonly int $currentInstallment,
        public readonly string $cardUniqueReference,
        public readonly int $cardTypeId,
        public readonly ?int $digitalWalletId = null,
    ) {
    }

    /** @phpstan-param  TransactionArray  $attributes */
    public static function create(array $attributes): self
    {
        return new self(...[
            ...$attributes,
            'statusId' => TransactionStatus::from($attributes['statusId']),
            'transactionTypeId' => TransactionType::from($attributes['transactionTypeId']),
        ]);
    }
}
