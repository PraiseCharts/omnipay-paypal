<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\RestGateway;

class RestReactivateSubscriptionRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestReactivateSubscriptionRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestReactivateSubscriptionRequest($client, $request);

        $this->request->initialize(array(
            'transactionReference'  => 'ABC-123',
            'description'           => 'Reactivate this subscription',
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('Reactivate this subscription', $data['note']);
    }
}
