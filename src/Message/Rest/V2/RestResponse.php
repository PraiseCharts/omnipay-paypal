<?php
/**
 * PayPal REST Response
 */

namespace Omnipay\PayPal\Message\Rest\V2;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * PayPal REST Response
 */
class RestResponse extends AbstractResponse
{
    protected $statusCode;

    public function __construct(RequestInterface $request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    public function isSuccessful()
    {
        return empty($this->data['error']) && $this->getCode() < 400;
    }

    public function getTransactionReference()
    {
        // This is usually correct for payments, authorizations, etc
        if (!empty($this->data['transactions']) && !empty($this->data['transactions'][0]['related_resources'])) {
            foreach (array('sale', 'authorization') as $type) {
                if (!empty($this->data['transactions'][0]['related_resources'][0][$type])) {
                    return $this->data['transactions'][0]['related_resources'][0][$type]['id'];
                }
            }
        }

        // This is a fallback, but is correct for fetch transaction and possibly others
        if (!empty($this->data['id'])) {
            return $this->data['id'];
        }

        return null;
    }

    public function getMessage()
    {
        if (isset($this->data['error_description'])) {
            return $this->data['error_description'];
        }

        if (isset($this->data['message'])) {
            return $this->data['message'];
        }
        
        return null;
    }

    public function getCode()
    {
        return $this->statusCode;
    }

    /**
     * Get a customer reference, for createCustomer requests.
     *
     * @return string|null
     */
    public function getCustomerReference()
    {
        if (isset($this->data['customer']) && isset($this->data['customer']['merchant_customer_id'])) {
            return $this->data['customer']['merchant_customer_id'];
        }

        if (isset($this->data['customer']) && isset($this->data['customer']['id'])) {
            return $this->data['customer']['id'];
        }

        return null;
    }

    /**
     * Get a card reference, for createCard or createCustomer requests.
     *
     * @return string|null
     */
    public function getCardReference()
    {
        if (!empty($this->data['id'])) {
            return $this->data['id'];
        } 

        return null;
    }

    /**
     * Get the card data from the response of purchaseRequest.
     *
     * @return array|null
     */
    public function getPaymentSource()
    {
        if (isset($this->data['payment_source'])) {
            return $this->data['payment_source'];
        }

        return null;
    }
}
