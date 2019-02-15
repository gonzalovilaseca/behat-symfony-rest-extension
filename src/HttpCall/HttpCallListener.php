<?php

namespace Gvf\SymfonyRestExtension\HttpCall;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HttpCallListener implements EventSubscriberInterface
{
    /** @var RestContextVoter */
    private $contextSupportedVoter;

    /** @var HttpCallResultPool */
    private $httpCallResultPool;

    public function __construct(RestContextVoter $contextSupportedVoter, HttpCallResultPool $httpCallResultPool)
    {
        $this->contextSupportedVoter = $contextSupportedVoter;
        $this->httpCallResultPool = $httpCallResultPool;
    }

    public static function getSubscribedEvents()
    {
        return [
            StepTested::AFTER => 'afterStep',
        ];
    }

    public function afterStep(AfterStepTested $event): void
    {
        $testResult = $event->getTestResult();

        if (!$testResult instanceof ExecutedStepResult) {
            return;
        }

        $httpCallResult = new HttpCallResult(
            $testResult->getCallResult()->getReturn()
        );

        if ($this->contextSupportedVoter->vote($httpCallResult)) {
            $this->httpCallResultPool->store($httpCallResult);
        }
    }
}
