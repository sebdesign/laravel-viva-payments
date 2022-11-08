<?php

namespace Sebdesign\VivaPayments\Requests;

class CreatePaymentOrder
{
    public function __construct(
        /** @var int<30,max> */
        public int $amount,
        public ?string $customerTrns = null,
        public ?Customer $customer = null,
        /** @var int<0,432000> */
        public int $paymentTimeOut = 1800,
        public bool $preauth = false,
        public bool $allowRecurring = false,
        /** @var int<0,36> */
        public int $maxInstallments = 0,
        public bool $paymentNotification = false,
        /** @var int<0,max> */
        public int $tipAmount = 0,
        public bool $disableExactAmount = false,
        public bool $disableCash = false,
        public bool $disableWallet = false,
        public string $sourceCode = 'Default',
        public ?string $merchantTrns = null,
        /** @var string[]|null */
        public ?array $tags = null,
        /** @var string[]|null */
        public ?array $cardTokens = null,
    ) {
    }
}
