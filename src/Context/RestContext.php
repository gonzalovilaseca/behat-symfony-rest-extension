<?php

namespace Gvf\SymfonyRestExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final class RestContext implements Context, KernelAwareContext
{
    use KernelDictionary {
        setKernel as Symfony2ExtensionSetKernel;
    }

    /**
     * @var Client
     */
    protected $client;

    /** @var Response */
    private $response;

    /** @var array */
    private $headers = [];

    public function setKernel(KernelInterface $kernel)
    {
        $this->Symfony2ExtensionSetKernel($kernel);

        $this->client = new Client($kernel);
    }

    /**
     * @Given I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, TableNode $data)
    {
        $files = [];
        $parameters = [];

        foreach ($data->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }

            $parameters[$row['key']] = $row['value'];
        }

        return $this->sendRequest($method, $url, $parameters, $files);
    }

    private function sendRequest(string $method, string $url, array $parameters = [], array $files = [], string $body = null)
    {
        $this->client->request(
            $method,
            $url,
            $parameters,
            $files,
            $this->headers,
            $body
        );

        //move to request object?
        $this->headers = [];
        $this->response = $this->client->getResponse();

        return $this->response;
    }

    /**
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body)
    {
        return $this->sendRequest($method, $url, [], [], $body);
    }

    /**
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, PyStringNode $body = null, $files = [])
    {
        return $this->sendRequest(
            $method,
            $url,
            [],
            $files,
            $body !== null ? $body->getRaw() : null
        );
    }

    /**
     * @Then the response should be equal to
     * @Then the response should be equal to:
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        throw new PendingException();
    }

    /**
     * @Then the response should be empty
     */
    public function theResponseShouldBeEmpty()
    {
        throw new PendingException();
    }

    /**
     * @Then the header :name should be equal to :value
     */
    public function theHeaderShouldBeEqualTo($name, $value)
    {
        $actual = $this->response->headers->get($name);

        Assert::assertEquals(strtolower($value), strtolower($actual),
            "The header '$name' should be equal to '$value', but it is: '$actual'"
        );
    }

    /**
     * @Then the header :name should not be equal to :value
     */
    public function theHeaderShouldNotBeEqualTo($name, $value)
    {
        throw new PendingException();
    }

    /**
     * @Then the header :name should contain :value
     */
    public function theHeaderShouldContain($name, $value)
    {
        throw new PendingException();
    }

    /**
     * @Then the header :name should not contain :value
     */
    public function theHeaderShouldNotContain($name, $value)
    {
        throw new PendingException();
    }

    /**
     * @Then the header :name should not exist
     */
    public function theHeaderShouldNotExist($name)
    {
        throw new PendingException();
    }

    /**
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        throw new PendingException();
    }

    /**
     * Add an header element in a request
     *
     * @Then I add :name header equal to :value
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @Then the response should be encoded in :encoding
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        throw new PendingException();

    }

    /**
     * @Then the response status code should be :statusCode
     */
    public function theResponseStatusCodeShouldBe($statusCode)
    {
        Assert::assertEquals($statusCode, $this->response->getStatusCode());
    }

    /**
     * @Then the response should be in JSON
     */
    public function theResponseShouldBeInJson()
    {
        $this->response->headers->contains(
            'Content-Type',
            'application/json'
        );
    }
}
