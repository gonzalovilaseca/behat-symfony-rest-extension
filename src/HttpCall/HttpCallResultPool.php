<?php

namespace Gvf\SymfonyRestExtension\HttpCall;

final class HttpCallResultPool
{
    /**
     * @var HttpCallResult|null
     */
    private $result;

    public function store(HttpCallResult $result): void
    {
        $this->result = $result;
    }

    public function getResult(): ?HttpCallResult
    {
        return $this->result;
    }
}
