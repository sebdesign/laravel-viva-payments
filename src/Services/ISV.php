<?php

namespace Sebdesign\VivaPayments\Services;

use Sebdesign\VivaPayments\Client;

class ISV
{
    public function __construct(protected Client $client) {}

    public function orders(): ISV\Order
    {
        return new ISV\Order($this->client);
    }

    public function sources(): ISV\Source
    {
        return new ISV\Source($this->client);
    }

    public function transactions(): ISV\Transaction
    {
        return new ISV\Transaction($this->client);
    }
}
