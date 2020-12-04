<?php

namespace spec\Gvf\SymfonyRestExtension\Context;

use Behat\Gherkin\Node\PyStringNode;
use Gvf\SymfonyRestExtension\Context\JsonContext;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallResult;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallResultPool;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @mixin JsonContext
 */
class JsonContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JsonContext::class);
    }

    function let(HttpCallResultPool $httpCallResultPool)
    {
        $this->beConstructedWith($httpCallResultPool);
    }

    function it_fails_if_json_are_different(HttpCallResultPool $httpCallResultPool)
    {
        $expected =
            \json_encode(['a' => 'b', 'c' => 'd',]);

        $actual = explode(PHP_EOL, '{
            "a":"b",
            "e":"f"
            }'
        );

        $pyStringNode = new PyStringNode($actual, 0);
        $httpCallResult = new HttpCallResult($expected);

        $httpCallResultPool->getResult()->shouldBeCalled()->willReturn($httpCallResult);

        $expectationFailedException = new AssertionFailedError(
            'Failed to assert that two json object where equal, found the following differences:\n
new: {"e":"f"}\n
removed: {"c":"d"}\n
edited: []',
        );

        $this
            ->shouldThrow($expectationFailedException)
            ->during('theJsonShouldBeEqualTo', [$pyStringNode])
        ;
    }
}
