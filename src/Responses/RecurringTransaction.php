<?php

namespace Sebdesign\VivaPayments\Responses;

use Sebdesign\VivaPayments\Enums\TransactionStatus;

class RecurringTransaction
{
    public function __construct(
        public readonly ?string $Emv,
        public readonly float $Amount,
        public readonly TransactionStatus $StatusId,
        public readonly ?string $RedirectUrl,
        public readonly string $CurrencyCode,
        public readonly string $TransactionId,
        public readonly int $ReferenceNumber,
        public readonly string $AuthorizationId,
        public readonly string $RetrievalReferenceNumber,
        public readonly ?string $Loyalty,
        public readonly int $ThreeDSecureStatusId,
        public readonly int $ErrorCode,
        public readonly ?string $ErrorText,
        public readonly string $TimeStamp,
        public readonly ?string $CorrelationId,
        public readonly int $EventId,
        public readonly bool $Success,
    ) {
    }

    /** @phpstan-param  RecurringTransactionArray  $attributes */
    public static function create(array $attributes): self
    {
        return new self(...[
            ...$attributes,
            'StatusId' => TransactionStatus::from($attributes['StatusId']),
        ]);
    }
}
