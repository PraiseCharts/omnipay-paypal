<?php

namespace Omnipay\PayPal\Message\Rest\V2;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class RestDeleteCardRequestTest extends TestCase
{
    /** @var RestDeleteCardRequest */
    private $request;

    /** @var CreditCard */
    private $card;

    public function setUp() : void
    {
        parent::setUp();

        $this->request = new RestDeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setCardReference('4w740078t47817438');
    }

    public function testEndpoint()
    {
        $this->request->setCardReference('4w740078t47817438');
        $endpoint = $this->request->getEndpoint();
        $this->assertSame('https://api.paypal.com/v3/vault/payment-tokens/4w740078t47817438', $this->request->getEndpoint());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('../../../../Rest/V2/Mock/RestDeleteCardSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getMessage());
    }

}
