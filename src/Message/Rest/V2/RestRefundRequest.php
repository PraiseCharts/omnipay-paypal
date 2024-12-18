<?php

/**
 * PayPal Create Credit Card Request.
 */
namespace Omnipay\PayPal\Message\Rest\V2;

/**
 * PayPal REST Refund Request
 *
 * To get details about completed payments (sale transaction) created by a payment request
 * or to refund a direct sale transaction, PayPal provides the /sale resource and related
 * sub-resources.
 *
 * TODO: There might be a problem here, in that refunding a capture requires a different URL.
 *
 * TODO: Yes I know. The gateway doesn't yet support looking up or refunding captured
 * transactions.  That will require adding additional message classes because the URLs
 * are all different.
 *
 * A non-zero amount can be provided for the refund using setAmount(), if this is not
 * provided (or is zero) then a full refund is made.
 *
 * Example -- note this example assumes that the purchase has been successful
 * and that the transaction ID returned from the purchase is held in $sale_id.
 * See RestPurchaseRequest for the first part of this example transaction:
 *
 * <code>
 *   $transaction = $gateway->refund(array(
 *       'amount'    => '10.00',
 *       'currency'  => 'AUD',
 *   ));
 *   $transaction->setTransactionReference($sale_id);
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Refund transaction was successful!\n";
 *       $data = $response->getData();
 *       echo "Gateway refund response data == " . print_r($data, true) . "\n";
 *   }
 * </code>
 *
 * @see RestRefundRequest
 * @link https://developer.paypal.com/docs/api/payments/v2/#captures_refund
 */
class RestRefundRequest extends AbstractRestRequest
{
    public function getData()
    {
        $this->validate('transactionReference');

        if ($this->getAmount() > 0) {
            return array(
                'amount' => array(
                    'value' => $this->getAmount(),
                    'currency_code' => $this->getCurrency(),
                ),
                'invoice_number' => $this->getTransactionId()
            );
        } else {
            return new \stdClass();
        }
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/payments/captures/' . $this->getTransactionReference() . '/refund';
    }
}