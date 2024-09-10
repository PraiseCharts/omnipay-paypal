<?php

/**
 * PayPal Create Credit Card Request.
 */
namespace Omnipay\PayPal\Message\Rest\V2;

/**
 * PayPal Create Credit Card Request.
 *
 * The Payment Method Tokens API saves payment methods so payers don't
 * have to enter details for future transactions. Payers can check out 
 * faster or pay without being present after they agree to save a payment
 * method.
 *
 * This call can be used to create a new customer or add a card
 * to an existing customer.  If a customerReference is passed in then
 * a card is added to an existing customer.  If there is no
 * customerReference passed in then a new customer is created.  The
 * response in that case will then contain both a customer token
 * and a card token, and is essentially the same as CreateCustomerRequest
 *
 * ### Example
 *
 * This example assumes that you have already created a
 * customer, and that the customer reference is stored in $customer_id.
 * See CreateCustomerRequest for the first part of this transaction.
 *
 * <code>
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   // The CreditCard object is also used for creating customers.
 *   $new_card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '5555555555554444',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '456',
 *               'email'                 => 'customer@example.com',
 *               'billingAddress1'       => '1 Lower Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Upper Swan',
 *               'billingPostcode'       => '6999',
 *               'billingState'          => 'WA',
 *   ));
 *
 *   // Do a create card transaction on the gateway
 *   $response = $gateway->createCard(array(
 *       'card'              => $new_card,
 *       'customer' => array(
 *                       'id => $customer_id,
 *                       'merchant_customer_id' => '123456',
 *                      ),  
 *   ))->send();
 *   if ($response->isSuccessful()) {
 *       echo "Gateway createCard was successful.\n";
 *       // Find the card ID
 *       $card_id = $response->getCardReference();
 *       echo "Card ID = " . $card_id . "\n";
 *   }
 * </code>
 *
 * @see CreateCustomerRequest
 * @link https://developer.paypal.com/docs/api/payment-tokens/v3/#payment-tokens_create
 */
class RestCreateCardRequest extends AbstractRestRequest
{
    public function getData()
    {
        $data = array();

        if ($this->getPaymentSource()) {
            $data['payment_source'] = $this->getPaymentSource();
        } elseif ($this->getCardReference()) {
            $data['payment_source']['card'] = $this->getCardReference();
        } elseif ($this->getCard()) {
            $this->getCard()->validate();
            $cardData = $this->getCardData();
            if (isset($cardData['brand'])) {
                $cardData['brand'] = strtoupper($cardData['brand']);
            }
            $data['payment_source']['card'] = $cardData;
        } else {
            // one of token or card is required
            $this->validate('payment_source');
        }

        if ($this->getCustomerReference()) {
            $data['customer']['merchant_customer_id'] = $this->getCustomerReference();
        }

        return $data;
    }

    protected function getEndpoint()
    {
        $this->setApiVersion('v3');
        return parent::getEndpoint() . '/vault/payment-tokens';
    }

    public function getCardData()
    {
        $data = parent::getCardData();
        unset($data['email']);

        return $data;
    }
}