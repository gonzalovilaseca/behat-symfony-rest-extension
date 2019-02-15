<?php

namespace Gvf\SymfonyRestExtension\HttpCall;

final class HttpCallResult
{
    /** @var mixed  */
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function update($value): void
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
