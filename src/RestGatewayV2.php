<?php
/**
 * PayPal Pro Class using REST API
 */

namespace Omnipay\PayPal;

use Omnipay\Common\AbstractGateway;

/**
 * PayPal Pro Class using REST API
 *
 * This class forms the gateway class for PayPal REST requests via the PayPal REST APIs.
 *
 * The PayPal API uses HTTP verbs and a RESTful endpoint structure. OAuth 2.0 is used
 * as the API Authorization framework. Request and response payloads are formatted as JSON.
 *
 * The PayPal REST APIs are supported in two environments. Use the Sandbox environment
 * for testing purposes, then move to the live environment for production processing.
 * When testing, generate an access token with your test credentials to make calls to
 * the Sandbox URIs. When you’re set to go live, use the live credentials assigned to
 * your app to generate a new access token to be used with the live URIs.
 *
 * ### Test Mode
 *
 * In order to use this for testing in sandbox mode you will need at least two sandbox
 * test accounts.  One will need to be a business account, and one will need to be a
 * personal account with credit card details.  To create these you will need to go to
 * the sandbox accounts section of the PayPal developer dashboard, here:
 * https://developer.paypal.com/webapps/developer/applications/accounts
 * On that page click "Create Account" and follow the prompts.  When you are creating the
 * Personal account, ensure that it is created with a credit card -- either Visa or
 * MasterCard or one of the other types.  When you are testing in the sandbox, use the
 * credit card details you will receive for this Personal account rather than any other
 * commonly used test credit card numbers (e.g. visa card 4111111111111111 or 4444333322221111
 * both of which will result in Error 500 / INTERNAL_SERVICE_ERROR type errors from the
 * PayPal gateway).
 *
 * With each API call, you’ll need to set request headers, including an OAuth 2.0
 * access token. Get an access token by using the OAuth 2.0 client_credentials token
 * grant type with your clientId:secret as your Basic Auth credentials. For more
 * information, see Make your first call (link).  This class sets all of the headers
 * associated with the API call for you, including making preliminary calls to create
 * or update the OAuth 2.0 access token before each call you make, if required.  All
 * you need to do is provide the clientId and secret when you initialize the gateway,
 * or use the set*() calls to set them after creating the gateway object.
 *
 * ### Credentials
 *
 * To create production and sandbox credentials for your PayPal account:
 *
 * * Log into your PayPal account.
 * * Navigate to your Sandbox accounts at https://developer.paypal.com/webapps/developer/applications/accounts
 *   to ensure that you have a valid sandbox account to use for testing.  If you don't already have a sandbox
 *   account, one can be created on this page.  You will actually need 2 accounts, a personal account and a
 *   business account, the business account is the one you need to use for creating API applications.
 * * Check your account status on https://developer.paypal.com/webapps/developer/account/status to ensure
 *   that it is valid for live transactions.
 * * Navigate to the My REST apps page: https://developer.paypal.com/webapps/developer/applications/myapps
 * * Click *Create App*
 * * On the next page, enter an App name and select the sandbox account to use, then click *Create app*.
 * * On the next page the sandbox account, endpoint, Client ID and Secret should be displayed.
 *   Record these.  The Sandbox account should match the one that you selected on the previous
 *   page, and the sandbox endpoint should be ai.sandbox.paypal.com
 * * Adjacent to *Live credentials* click *Show* to display your live credentials.  The endpoint
 *   for these should be api.paypal.com, there should also be a Client ID and Secret.
 *
 * You can create additional REST APIs apps for other websites -- because the webhooks are
 * stored per app then it pays to have one API app per website that you are using (and an
 * additional one for things like command line testing, etc).
 *
 * ### Example
 *
 * #### Initialize Gateway
 *
 * <code>
 *   // Create a gateway for the PayPal RestGateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('PayPal_Rest_V2');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'clientId' => 'MyPayPalClientId',
 *       'secret'   => 'MyPayPalSecret',
 *       'testMode' => true, // Or false when you are ready for live transactions
 *   ));
 * </code>
 *
 * #### Direct Credit Card Payment
 *
 * <code>
 *   // Create a credit card object
 *   // DO NOT USE THESE CARD VALUES -- substitute your own
 *   // see the documentation in the class header.
 *   $card = new CreditCard(array(
 *               'firstName' => 'Example',
 *               'lastName' => 'User',
 *               'number' => '4111111111111111',
 *               'expiryMonth'           => '01',
 *               'expiryYear'            => '2020',
 *               'cvv'                   => '123',
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 *   // Do a purchase transaction on the gateway
 *   try {
 *       $transaction = $gateway->purchase(array(
 *           'amount'        => '10.00',
 *           'currency'      => 'AUD',
 *           'description'   => 'This is a test purchase transaction.',
 *           'card'          => $card,
 *       ));
 *       $response = $transaction->send();
 *       $data = $response->getData();
 *       echo "Gateway purchase response data == " . print_r($data, true) . "\n";
 *
 *       if ($response->isSuccessful()) {
 *           echo "Purchase transaction was successful!\n";
 *       }
 *   } catch (\Exception $e) {
 *       echo "Exception caught while attempting authorize.\n";
 *       echo "Exception type == " . get_class($e) . "\n";
 *       echo "Message == " . $e->getMessage() . "\n";
 *   }
 * </code>
 *
 * ### Dashboard
 *
 * Once you have processed some payments you can go to the PayPal sandbox site,
 * at https://www.sandbox.paypal.com/ and log in with the email address and password
 * of your PayPal sandbox business test account.  You will then see the result
 * of those transactions on the "My recent activity" list under the My Account
 * tab.
 *
 * @link https://developer.paypal.com/docs/api/
 * @link https://devtools-paypal.com/integrationwizard/
 * @link http://paypal.github.io/sdk/
 * @link https://developer.paypal.com/docs/integration/direct/rest_api_payment_country_currency_support/
 * @link https://developer.paypal.com/docs/faq/
 * @link https://developer.paypal.com/docs/integration/direct/make-your-first-call/
 * @link https://developer.paypal.com/docs/integration/web/accept-paypal-payment/
 * @link https://developer.paypal.com/docs/api/#authentication--headers
 * @see Omnipay\PayPal\Message\Rest\V1\AbstractRestRequest
 */
class RestGatewayV2 extends AbstractGateway
{

    // Constants used in plan creation
    const BILLING_PLAN_TYPE_FIXED       = 'FIXED';
    const BILLING_PLAN_TYPE_INFINITE    = 'INFINITE';
    const BILLING_PLAN_FREQUENCY_DAY    = 'DAY';
    const BILLING_PLAN_FREQUENCY_WEEK   = 'WEEK';
    const BILLING_PLAN_FREQUENCY_MONTH  = 'MONTH';
    const BILLING_PLAN_FREQUENCY_YEAR   = 'YEAR';
    const BILLING_PLAN_STATE_CREATED    = 'CREATED';
    const BILLING_PLAN_STATE_ACTIVE     = 'ACTIVE';
    const BILLING_PLAN_STATE_INACTIVE   = 'INACTIVE';
    const BILLING_PLAN_STATE_DELETED    = 'DELETED';
    const PAYMENT_TRIAL                 = 'TRIAL';
    const PAYMENT_REGULAR               = 'REGULAR';

    public function getName()
    {
        return 'PayPal REST V2';
    }

    public function getDefaultParameters()
    {
        return array(
            'clientId'     => '',
            'secret'       => '',
            'token'        => '',
            'testMode'     => false,
        );
    }

    //
    // Tokens -- methods to set up, store and retrieve the OAuth 2.0 access token.
    //
    // @link https://developer.paypal.com/docs/api/#authentication--headers
    //

    /**
     * Get OAuth 2.0 client ID for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    /**
     * Set OAuth 2.0 client ID for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @param string $value
     * @return RestGateway provides a fluent interface
     */
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * Get OAuth 2.0 secret for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * Set OAuth 2.0 secret for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @param string $value
     * @return RestGateway provides a fluent interface
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * Get OAuth 2.0 access token.
     *
     * @param bool $createIfNeeded [optional] - If there is not an active token present, should we create one?
     * @return string
     */
    public function getToken($createIfNeeded = true)
    {
        if ($createIfNeeded && !$this->hasToken()) {
            $response = $this->createToken()->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                if (isset($data['access_token'])) {
                    $this->setToken($data['access_token']);
                    $this->setTokenExpires(time() + $data['expires_in']);
                }
            }
        }

        return $this->getParameter('token');
    }

    /**
     * Create OAuth 2.0 access token request.
     *
     * @return \Omnipay\PayPal\Message\Rest\V1\RestTokenRequest
     */
    public function createToken()
    {
        return $this->createRequest('\Omnipay\PayPal\Message\Rest\V1\RestTokenRequest', array());
    }

    /**
     * Set OAuth 2.0 access token.
     *
     * @param string $value
     * @return RestGateway provides a fluent interface
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Get OAuth 2.0 access token expiry time.
     *
     * @return integer
     */
    public function getTokenExpires()
    {
        return $this->getParameter('tokenExpires');
    }

    /**
     * Set OAuth 2.0 access token expiry time.
     *
     * @param integer $value
     * @return RestGateway provides a fluent interface
     */
    public function setTokenExpires($value)
    {
        return $this->setParameter('tokenExpires', $value);
    }

    /**
     * Is there a bearer token and is it still valid?
     *
     * @return bool
     */
    public function hasToken()
    {
        $token = $this->getParameter('token');

        $expires = $this->getTokenExpires();
        if (!empty($expires) && !is_numeric($expires)) {
            $expires = strtotime($expires);
        }

        return !empty($token) && time() < $expires;
    }

    public function setPaymentSource($value)
    {
        return $this->setParameter('paymentSource', $value);
    }

    /**
     * Create Request
     *
     * This overrides the parent createRequest function ensuring that the OAuth
     * 2.0 access token is passed along with the request data -- unless the
     * request is a RestTokenRequest in which case no token is needed.  If no
     * token is available then a new one is created (e.g. if there has been no
     * token request or the current token has expired).
     *
     * @param string $class
     * @param array $parameters
     * @return \Omnipay\PayPal\Message\Rest\V1\AbstractRestRequest|\Omnipay\PayPal\Message\Rest\V2\AbstractRestRequest
     */
    public function createRequest($class, array $parameters = array())
    {
        if (!$this->hasToken() && $class != '\Omnipay\PayPal\Message\Rest\V1\RestTokenRequest') {
            // This will set the internal token parameter which the parent
            // createRequest will find when it calls getParameters().
            $this->getToken(true);
        }

        return parent::createRequest($class, $parameters);
    }

    
    // TODO: Methods To Implement
    
    // Create Credit Card
    // createCard
    // updateCard
    // deleteCard 

    //
    // Cards
    // @link https://developer.paypal.com/docs/api/payment-tokens/v3/
    //

    /**
     * @inheritdoc
     *
     * @return \Omnipay\PayPal\Message\Rest\V2\RestCreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayPal\Message\Rest\V2\RestCreateCardRequest', $parameters);
    }

        /**
     * @inheritdoc
     *
     * @return \Omnipay\PayPal\Message\Rest\V2\RestCreateCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayPal\Message\Rest\V2\RestDeleteCardRequest', $parameters);
    }

    // /**
    //  * @inheritdoc
    //  *
    //  * @return \Omnipay\PayPal\Message\Rest\V2\UpdateCardRequest
    //  */
    // public function updateCard(array $parameters = array())
    // {
    //     return $this->createRequest('\Omnipay\PayPal\Message\Rest\V2\UpdateCardRequest', $parameters);
    // }

    // /**
    //  * @inheritdoc
    //  *
    //  * @return \Omnipay\PayPal\Message\Rest\V2\DeleteCardRequest
    //  */
    // public function deleteCard(array $parameters = array())
    // {
    //     return $this->createRequest('\Omnipay\PayPal\Message\Rest\V2\DeleteCardRequest', $parameters);
    // }

    // Purchase
    
    // Refund

    // Void

    // 
    

}
