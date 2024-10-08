<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\RestGateway;

class RestSuspendSubscriptionRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestSuspendSubscriptionRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestSuspendSubscriptionRequest($client, $request);

        $this->request->initialize(array(
            'transactionReference'  => 'ABC-123',
            'description'           => 'Suspend this subscription',
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('Suspend this subscription', $data['note']);
    }
}
