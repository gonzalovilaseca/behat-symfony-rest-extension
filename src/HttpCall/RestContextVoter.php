<?php

namespace Gvf\SymfonyRestExtension\HttpCall;

use Symfony\Component\HttpFoundation\Response;

final class RestContextVoter
{
    public function vote(HttpCallResult $httpCallResult): bool
    {
        if ($httpCallResult->getValue() instanceof Response) {
            $httpCallResult->update($httpCallResult->getValue()->getContent());

            return true;
        }

        return false;
    }
}
