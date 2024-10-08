<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;

class RestFetchPurchaseRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestFetchPurchaseRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestFetchPurchaseRequest($client, $request);
    }

    public function testEndpoint()
    {
        $this->request->setTransactionReference('ABC-123');
        $this->assertStringEndsWith('/payments/payment/ABC-123', $this->request->getEndpoint());
    }
}
