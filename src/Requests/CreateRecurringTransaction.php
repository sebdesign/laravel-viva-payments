<?php

namespace Sebdesign\VivaPayments\Requests;

class CreateRecurringTransaction
{
    public function __construct(
        /** @var int<0,max> */
        public int $amount,
        /** @var int<1, 36>|null */
        public ?int $installments = null,
        public ?string $customerTrns = null,
        public ?string $merchantTrns = null,
        public string $sourceCode = 'Default',
        /** @var int<0,max> */
        public int $tipAmount = 0,
    ) {
    }
}
