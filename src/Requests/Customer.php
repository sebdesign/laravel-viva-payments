<?php

namespace Sebdesign\VivaPayments\Requests;

class Customer
{
    public function __construct(
        public ?string $email = null,
        public ?string $fullName = null,
        public ?string $phone = null,
        public ?string $countryCode = null,
        public ?string $requestLang = null,
    ) {
    }
}
