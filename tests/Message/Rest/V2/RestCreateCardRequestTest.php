<?php

namespace Omnipay\PayPal\Message\Rest\V2;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class RestCreateCardRequestTest extends TestCase
{
    /** @var RestCreateCardRequest */
    protected $request;

    /** @var CreditCard */
    protected $card;

    public function setUp() : void
    {
        parent::setUp();

        $this->request = new RestCreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $card = $this->getValidCard();
        $this->card = new CreditCard($card);

        $this->request->initialize(array('card' => $card));
    }

    public function testGetData()
    {
        $card = $this->card;
        $data = $this->request->getData();

        $cardFromRequest = $data['payment_source']['card'];

        $this->assertSame($card->getNumber(), $cardFromRequest['number']);
        $this->assertSame($card->getBrand(), $cardFromRequest['brand']);
        $this->assertSame($card->getExpiryYear().'-'.$card->getExpiryMonth(), $cardFromRequest['expiry']);
        $this->assertSame($card->getCvv(), $cardFromRequest['security_code']);
        $this->assertSame($card->getName(), $cardFromRequest['name']);
        $this->assertSame($card->getAddress1(), $cardFromRequest['billing_address']['address_line_1']);
        $this->assertSame($card->getAddress2(), $cardFromRequest['billing_address']['address_line_2']);
        $this->assertSame($card->getCity(), $cardFromRequest['billing_address']['admin_area_2']);
        $this->assertSame($card->getState(), $cardFromRequest['billing_address']['admin_area_1']);
        $this->assertSame($card->getPostcode(), $cardFromRequest['billing_address']['postal_code']);
        $this->assertSame($card->getCountry(), $cardFromRequest['billing_address']['country_code']);
    }
}
