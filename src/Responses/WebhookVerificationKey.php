<?php

namespace Sebdesign\VivaPayments\Responses;

class WebhookVerificationKey
{
    public function __construct(public readonly string $Key)
    {
    }
}
