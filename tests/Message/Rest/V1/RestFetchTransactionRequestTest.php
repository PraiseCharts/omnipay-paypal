<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;

class RestFetchTransactionRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestFetchTransactionRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestFetchTransactionRequest($client, $request);
    }

    public function testGetData()
    {
        $this->request->setTransactionReference('ABC-123');
        $data = $this->request->getData();
        $this->assertEquals(array(), $data);
    }

    public function testEndpoint()
    {
        $this->request->setTransactionReference('ABC-123');
        $this->assertStringEndsWith('/payments/sale/ABC-123', $this->request->getEndpoint());
    }
}
