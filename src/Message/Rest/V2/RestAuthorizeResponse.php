<?php
/**
 * PayPal REST Authorize Response
 */

namespace Omnipay\PayPal\Message\Rest\V2;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayPal REST Authorize Response
 */
class RestAuthorizeResponse extends RestResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        $everyCaptureCompleted = true;
        
        if (!isset($this->data['purchase_units'])) {
            return false;
        }
        
        foreach ($this->data['purchase_units'] as $purchaseUnit) {
            if (isset($purchaseUnit['payments']['captures'])) {
            foreach ($purchaseUnit['payments']['captures'] as $capture) {
                if ($capture['status'] !== 'COMPLETED') {
                    $everyCaptureCompleted = false;
                    break 2; // Exit both foreach loops
                }
            }
            }
        }

        return empty($this->data['error']) && $this->getCode() == 201 && $everyCaptureCompleted;
    }

    public function getMessage()
    {
        if (isset($this->data['error'][0])) {
            return $this->data['error'];
        }

        $message = isset($this->data['purchase_units'][0]['payments']['captures'][0]['status']) 
        ? $this->data['purchase_units'][0]['payments']['captures'][0]['status'] 
        : null;
        return $message;

    }

    public function isRedirect()
    {
        return $this->getRedirectUrl() !== null;
    }

    public function getRedirectUrl()
    {
        if (isset($this->data['links']) && is_array($this->data['links'])) {
            foreach ($this->data['links'] as $key => $value) {
                if ($value['rel'] == 'approval_url') {
                    return $value['href'];
                }
            }
        }

        return null;
    }

    /**
     * Get the URL to complete (execute) the purchase or agreement.
     *
     * The URL is embedded in the links section of the purchase or create
     * subscription request response.
     *
     * @return string
     */
    public function getCompleteUrl()
    {
        if (isset($this->data['links']) && is_array($this->data['links'])) {
            foreach ($this->data['links'] as $key => $value) {
                if ($value['rel'] == 'execute') {
                    return $value['href'];
                }
            }
        }

        return null;
    }

    public function getTransactionReference()
    {
        // The transaction reference for a paypal purchase request or for a
        // paypal create subscription request ends up in the execute URL
        // in the links section of the response.
        $completeUrl = $this->getCompleteUrl();
        if (empty($completeUrl)) {
            return parent::getTransactionReference();
        }

        $urlParts = explode('/', $completeUrl);

        // The last element of the URL should be "execute"
        $execute = end($urlParts);
        if (!in_array($execute, array('execute', 'agreement-execute'))) {
            return parent::getTransactionReference();
        }

        // The penultimate element should be the transaction reference
        return prev($urlParts);
    }

    /**
     * Get the required redirect method (either GET or POST).
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     *
     * @return null
     */
    public function getRedirectData()
    {
        return null;
    }
}
