<?php

namespace Omnipay\PayPal;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

class RestGatewayV2Test extends GatewayTestCase
{
    /** @var RestGateway */
    public $gateway;

    /** @var array */
    public $options;

    /** @var array */
    public $subscription_options;

    public function setUp() : void
    {
        parent::setUp();

        $this->gateway = new RestGatewayV2($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setToken('TEST-TOKEN-123');
        $this->gateway->setTokenExpires(time() + 600);

        $this->options = array(
            'amount' => '10.00',
            'card' => new CreditCard(array(
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => date('Y'),
                'cvv' => '123',
            )),
        );

        $this->subscription_options = array(
            'transactionReference'  => 'ABC-1234',
            'description'           => 'Description goes here',
        );
    }

    public function testBearerToken()
    {
        $this->gateway->setToken('');
        $this->setMockHttpResponse('RestTokenSuccess.txt');

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A015GQlKQ6uCRzLHSGRliANi59BHw6egNVKEWRnxvTwvLr0', $this->gateway->getToken()); // triggers request
        $this->assertEquals(time() + 28800, $this->gateway->getTokenExpires());
        $this->assertTrue($this->gateway->hasToken());
    }

    public function testBearerTokenReused()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->setToken('MYTOKEN');
        $this->gateway->setTokenExpires(time() + 60);

        $this->assertTrue($this->gateway->hasToken());
        $this->assertEquals('MYTOKEN', $this->gateway->getToken());
    }

    public function testBearerTokenExpires()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->setToken('MYTOKEN');
        $this->gateway->setTokenExpires(time() - 60);

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A015GQlKQ6uCRzLHSGRliANi59BHw6egNVKEWRnxvTwvLr0', $this->gateway->getToken());
    }


    public function testCreateCard()
    {
        $this->setMockHttpResponse('RestCreateCardSuccess.txt');

        $response = $this->gateway->createCard($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('4w740078t47817438', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testDeleteCard()
    {
        $this->setMockHttpResponse('RestDeleteCardSuccess.txt');
        $response = $this->gateway->deleteCard(array('cardReference' => '4w740078t47817438'))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testCreateCardWithBillingAgreementAsPaymentSource()
    {
        $this->setMockHttpResponse('RestCreateCardSuccess.txt');

        $this->gateway->setPaymentSource(array("token" => array('id' => 'B-1E307824NA024813T', 'type' => 'BILLING_AGREEMENT')));
        $response = $this->gateway->createCard()->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('4w740078t47817438', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

}
