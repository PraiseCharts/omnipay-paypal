# Omnipay: PayPal

**PayPal driver for the Omnipay PHP payment processing library**

[![Unit Tests](https://github.com/thephpleague/omnipay-paypal/actions/workflows/run-tests.yml/badge.svg)](https://github.com/thephpleague/omnipay-paypal/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://poser.pugx.org/omnipay/paypal/version.png)](https://packagist.org/packages/omnipay/paypal)
[![Total Downloads](https://poser.pugx.org/omnipay/paypal/d/total.png)](https://packagist.org/packages/omnipay/paypal)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements PayPal support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `omnipay/paypal` with Composer:

```
composer require league/omnipay omnipay/paypal
```


## Basic Usage

The following gateways are provided by this package:

* `PayPal_Express` (PayPal Express Checkout)
* `PayPal_ExpressInContext` (PayPal Express In-Context Checkout)
* `PayPal_Pro` (PayPal Website Payments Pro)
* `PayPal_RestV1` (PayPal REST API v1 - Deprecated)
    * *Note: The `PayPal_Rest` gateway now points to PayPal_Rest_V1, which supports the v1 API routes. This version is deprecated and no longer recommended for new integrations.*
* `PayPal_RestV2` (PayPal REST API v2 & v3)
    * *This gateway supports the latest v2 and v3 API routes for card creation and transaction processing. New integrations should use this version.*

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Quirks

The transaction reference obtained from the purchase() response can't be used to refund a purchase. The transaction reference from the completePurchase() response is the one that should be used.

`PayPal_RestV1` is deprecated and may not support the latest PayPal features.

## Out Of Scope

Omnipay does not cover recurring payments or billing agreements, and so those features are not included in this package. Extensions to this gateway are always welcome. 

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-paypal/issues),
or better yet, fork the library and submit a pull request.
