<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;

class RestListPlanRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestListPlanRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestListPlanRequest($client, $request);
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertArrayHasKey('page',$data);
        $this->assertArrayHasKey('status',$data);
        $this->assertArrayHasKey('page_size',$data);
        $this->assertArrayHasKey('total_required',$data);
    }

    public function testEndpoint()
    {
        $this->assertStringEndsWith('/payments/billing-plans', $this->request->getEndpoint());
    }

}
