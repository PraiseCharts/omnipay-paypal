<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\RestGateway;

class RestUpdatePlanRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestUpdatePlanRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestUpdatePlanRequest($client, $request);

        $this->request->initialize(array(
            'transactionReference'  => 'ABC-123',
            'state'                 => 'ACTIVE',
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('/', $data[0]['path']);
        $this->assertEquals('ACTIVE', $data[0]['value']['state']);
    }
}
