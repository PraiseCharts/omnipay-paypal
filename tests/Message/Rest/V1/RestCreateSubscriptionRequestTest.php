<?php

namespace Omnipay\PayPal\Message\Rest\V1;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\RestGateway;

class RestCreateSubscriptionRequestTest extends TestCase
{
    /** @var \Omnipay\PayPal\Message\Rest\V1\RestCreateSubscriptionRequest */
    private $request;

    public function setUp() : void
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestCreateSubscriptionRequest($client, $request);

        $this->request->initialize(array(
            'name'                  => 'Test Subscription',
            'description'           => 'Test Billing Subscription',
            'startDate'             => new \DateTime('now', new \DateTimeZone('UTC')),
            'planId'                => 'ABC-123',
            'payerDetails'          => array(
                'payment_method'    => 'paypal',
            ),
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('Test Subscription', $data['name']);
        $this->assertEquals('Test Billing Subscription', $data['description']);
        $this->assertEquals('ABC-123', $data['plan']['id']);
        $this->assertEquals('paypal', $data['payer']['payment_method']);
    }
}
