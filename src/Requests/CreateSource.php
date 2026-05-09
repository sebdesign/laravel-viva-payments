<?php

namespace Sebdesign\VivaPayments\Requests;

class CreateSource
{
    public function __construct(
        public string $name,
        public string $sourceCode,
        public ?string $domain = null,
        public ?bool $isSecure = null,
        public ?string $pathFail = null,
        public ?string $pathSuccess = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?int $walletId = null,
        public ?bool $isPhysical = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $transactionDescriptor = null,
    ) {}

    public static function ecommerce(
        string $domain,
        bool $isSecure,
        string $name,
        string $pathFail,
        string $pathSuccess,
        string $sourceCode,
    ): self {
        return new self(
            name: $name,
            domain: $domain,
            isSecure: $isSecure,
            pathFail: $pathFail,
            pathSuccess: $pathSuccess,
            sourceCode: $sourceCode,
            isPhysical: false,
        );
    }
}
