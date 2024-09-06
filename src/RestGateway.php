<?php
/**
 * PayPal Pro Class using REST API
 */

namespace Omnipay\PayPal;

/**
 * Deprecated: Use PayPal_Rest_V1 instead
 */
class RestGateway extends RestGatewayV1
{

   function getShortName()
   {
      return 'PayPal_Rest';
   }

}
