<?php

namespace Sebdesign\VivaPayments;

use Exception;

class VivaException extends Exception
{
    public function __construct($message, $code)
    {
        parent::__construct("Error {$code}: {$message}", $code);
    }
}
