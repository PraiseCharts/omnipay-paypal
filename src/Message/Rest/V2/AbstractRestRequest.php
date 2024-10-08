<?php
/**
 * PayPal Abstract REST Request
 */

namespace Omnipay\PayPal\Message\Rest\V2;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * PayPal Abstract REST Request
 *
 * This class forms the base class for PayPal REST requests via the PayPal REST APIs.
 *
 * A complete REST operation is formed by combining an HTTP method (or “verb”) with
 * the full URI to the resource you’re addressing. For example, here is the operation
 * to create a new payment:
 *
 * <code>
 * POST https://api.paypal.com/v2/orders/create
 * </code>
 *
 * To create a complete request, combine the operation with the appropriate HTTP headers
 * and any required JSON payload.
 *
 * @link https://developer.paypal.com/api/rest/
 * @see Omnipay\PayPal\RestV2Gateway
 */
abstract class AbstractRestRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * Sandbox Endpoint URL
     *
     * The PayPal REST APIs are supported in two environments. Use the Sandbox environment
     * for testing purposes, then move to the live environment for production processing.
     * When testing, generate an access token with your test credentials to make calls to
     * the Sandbox URIs. When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $testEndpoint = 'https://api-m.sandbox.paypal.com';

    /**
     * Live Endpoint URL
     *
     * When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://api-m.paypal.com';

    /**
     * PayPal Payer ID
     *
     * @var string PayerID
     */
    protected $payerId = null;

    protected $referrerCode;
    protected $requestId;

    /**
     * @var bool
     */
    protected $negativeAmountAllowed = true;

    protected $apiVersion = 'v2';
    
    /**
     * @return string
     */
    public function getReferrerCode()
    {
        return $this->referrerCode;
    }

    /**
     * @param string $referrerCode
     */
    public function setReferrerCode($referrerCode)
    {
        $this->referrerCode = $referrerCode;
    }

        /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }


    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    protected function getEndpoint()
    {
        $base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
        return $base . '/' . $this->apiVersion;
    }

    protected function setApiVersion($version)
    {
        $this->apiVersion = $version;
    }

    public function sendData($data)
    {

        // Guzzle HTTP Client createRequest does funny things when a GET request
        // has attached data, so don't send the data if the method is GET.
        if ($this->getHttpMethod() == 'GET') {
            $requestUrl = $this->getEndpoint() . '?' . http_build_query($data);
            $body = null;
        } else {
            $body = $this->toJSON($data);
            $requestUrl = $this->getEndpoint();
        }

        // Might be useful to have some debug code here, PayPal especially can be
        // a bit fussy about data formats and ordering.  Perhaps hook to whatever
        // logging engine is being used.
        // echo "Data == " . json_encode($data) . "\n";

        $headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-type' => 'application/json',
        );

        if ($this->getReferrerCode()) {
            $headers['PayPal-Partner-Attribution-Id'] = $this->getReferrerCode();
        }

        if ($this->getRequestId()) {
            $headers['PayPal-Request-Id'] = $this->getRequestId();
        } 

        try {
            $httpResponse = $this->httpClient->request(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                $headers,
                $body
            );
            // Empty response body should be parsed also as and empty array
            $body = (string) $httpResponse->getBody()->getContents();
            $jsonToArrayResponse = !empty($body) ? json_decode($body, true) : array();
            return $this->response = $this->createResponse($jsonToArrayResponse, $httpResponse->getStatusCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Returns object JSON representation required by PayPal.
     * The PayPal REST API requires the use of JSON_UNESCAPED_SLASHES.
     *
     * Adapted from the official PayPal REST API PHP SDK.
     * (https://github.com/paypal/PayPal-PHP-SDK/blob/master/lib/PayPal/Common/PayPalModel.php)
     *
     * @param int $options http://php.net/manual/en/json.constants.php
     * @return string
     */
    public function toJSON($data, $options = 0)
    {
        // Because of PHP Version 5.3, we cannot use JSON_UNESCAPED_SLASHES option
        // Instead we would use the str_replace command for now.
        // TODO: Replace this code with return json_encode($this->toArray(), $options | 64); once we support PHP >= 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new RestResponse($this, $data, $statusCode);
    }

    /**
     * @return mixed
     */
    public function getPaymentSource()
    {
        return $this->getParameter('paymentSource');
    }

    /**
     * @param $value
     *
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setPaymentSource($value)
    {
        return $this->setParameter('paymentSource', $value);
    } 

    /**
     * Get the customer reference.
     *
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference.
     *
     * Used when calling CreateCard on an existing customer.  If this
     * parameter is not set then a new customer is created.
     *
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Get the card data.
     *
     * Because the PayPal gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getCardData()
    {
        $card = $this->getCard();
        $card->validate();

        $data = array(
            'name' => $this->getCard()->getName(),
            'number' => $this->getCard()->getNumber(),
            'brand' => $this->getCard()->getBrand(),
            'expiry' => $this->getCard()->getExpiryDate('Y-m'),
            'security_code' => $this->getCard()->getCvv(),
            'billing_address' => array(
                'address_line_1' => $this->getCard()->getAddress1(),
                'address_line_2' => $this->getCard()->getAddress2(),
                'admin_area_2' => $this->getCard()->getCity(),
                'admin_area_1' => $this->getCard()->getState(),
                'postal_code' => $this->getCard()->getPostcode(),
                'country_code' => strtoupper($this->getCard()->getCountry()),
            )
        );

        return $data;
    }
}
