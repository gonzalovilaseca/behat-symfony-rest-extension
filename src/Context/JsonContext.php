<?php

namespace Gvf\SymfonyRestExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Nahid\JsonQ\Jsonq;
use PHPUnit\Framework\Assert;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallResultPool;

final class JsonContext implements Context
{
    /** @var HttpCallResultPool */
    protected $httpCallResultPool;

    public function __construct(HttpCallResultPool $httpCallResultPool)
    {
        $this->httpCallResultPool = $httpCallResultPool;
    }

    /**
     * @Then the JSON should be equal to:
     */
    public function theJsonShouldBeEqualTo(PyStringNode $string)
    {
        $json = new Jsonq();
        $json->json((string)$string);

        Assert::assertEquals((string)$json->toJson(), (string)$this->getJson()->toJson());
    }

    /**
     * @Then the response should not be in JSON
     */
    public function theResponseShouldNotBeInJson()
    {
        throw new PendingException();
    }

        /**
     * @Then the JSON should be like:
     */
    public function theJsonShouldBeLike(PyStringNode $expectedString)
    {
        $pattern = '/\n*/m';
        $replace = '';
        $removedLinebreaks = preg_replace($pattern, $replace, $expectedString);

        $pattern = '~"[^"]*"(*SKIP)(*F)|\s+~';
        $replace = '';
        $removedLinebaksAndWhitespace = preg_replace($pattern, $replace, $removedLinebreaks);

        // For chinese chars
        $actual = \json_encode(\json_decode($this->getJson()->toJson()), JSON_UNESCAPED_UNICODE);
        
        Assert::assertRegExp($removedLinebaksAndWhitespace, $actual);
    }
    
    /**
     * @Then the JSON nodes should be equal to:
     */
    public function theJsonNodesShouldBeEqualTo(TableNode $nodes)
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldBeEqualTo($node, $text);
        }
    }

    /**
     * @Then the JSON node :node should be equal to :text
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        Assert::assertEquals($text, $this->find($node));
    }

    /**
     * @Then the JSON node :node should match :pattern
     */
    public function theJsonNodeShouldMatch($node, $pattern)
    {
        throw new PendingException();
    }

    /**
     * @Then the JSON node :node should not be null
     */
    public function theJsonNodeShouldNotBeNull($node)
    {
        Assert::assertNotNull($this->find($node));
    }

    /**
     * @Then the JSON node :node should be null
     */
    public function theJsonNodeShouldBeNull($node)
    {
        Assert::assertNull($this->find($node));
    }

    /**
     * @Then the JSON node :node should be true
     */
    public function theJsonNodeShouldBeTrue($node)
    {
        Assert::assertTrue($this->find($node));
    }

    /**
     * @Then the JSON node :node should be false
     */
    public function theJsonNodeShouldBeFalse($node)
    {
        Assert::assertFalse($this->find($node));
    }

    /**
     * @Then the JSON node :node should be equal to the number :number
     */
    public function theJsonNodeShouldBeEqualToTheNumber($node, $number)
    {
        $actual = $this->find($node);

        Assert::assertEquals($number, $actual);
        Assert::assertIsInt($actual);
    }

    /**
     * @Then the JSON node :node should have :count element(s)
     */
    public function theJsonNodeShouldHaveElements($node, $count)
    {
        throw new PendingException();
    }

    /**
     * @Then the JSON nodes should contain:
     */
    public function theJsonNodesShouldContain(TableNode $nodes)
    {
        throw new PendingException();
//        foreach ($nodes->getRowsHash() as $node => $text) {
//            $this->theJsonNodeShouldContain($node, $text);
//        }
    }

    /**
     * @Then the JSON node :node should contain :text
     */
    public function theJsonNodeShouldContain($node, $text)
    {
        throw new PendingException();
    }

    /**
     * @Then the JSON nodes should not contain:
     */
    public function theJsonNodesShouldNotContain(TableNode $nodes)
    {
        throw new PendingException();
//        foreach ($nodes->getRowsHash() as $node => $text) {
//            $this->theJsonNodeShouldNotContain($node, $text);
//        }
    }

    /**
     * @Then the JSON node :node should not contain :text
     */
    public function theJsonNodeShouldNotContain($node, $text)
    {
        throw new PendingException();
    }

    /**
     * @Then the JSON node :name should not exist
     */
    public function theJsonNodeShouldNotExist($name)
    {
        throw new PendingException();
    }

    /**
     * @Then the JSON node :name should exist
     */
    public function theJsonNodeShouldExist($name)
    {
        throw new PendingException();
    }

    private function getJson(): Jsonq
    {
        $json = new Jsonq();

        return $json->json($this->httpCallResultPool->getResult()->getValue());
    }

    /**
     * @param mixed $node
     *
     * @return mixed
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     * @throws \Nahid\JsonQ\Exceptions\NullValueException
     */
    private function find($node)
    {
        return $this->getJson()->find($node);
    }
}
