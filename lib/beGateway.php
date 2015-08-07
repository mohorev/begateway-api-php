<?php

// Tested on PHP 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('beGateway needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('beGateway needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('beGateway needs the Multibyte String PHP extension.');
}

require_once (dirname(__FILE__) . '/beGateway/Settings.php');
require_once (dirname(__FILE__) . '/beGateway/Logger.php');
require_once (dirname(__FILE__) . '/beGateway/Language.php');
require_once (dirname(__FILE__) . '/beGateway/Customer.php');
require_once (dirname(__FILE__) . '/beGateway/Card.php');
require_once (dirname(__FILE__) . '/beGateway/Money.php');
require_once (dirname(__FILE__) . '/beGateway/ResponseBase.php');
require_once (dirname(__FILE__) . '/beGateway/Response.php');
require_once (dirname(__FILE__) . '/beGateway/ResponseCheckout.php');
require_once (dirname(__FILE__) . '/beGateway/ResponseCardToken.php');
require_once (dirname(__FILE__) . '/beGateway/ApiAbstract.php');
require_once (dirname(__FILE__) . '/beGateway/ChildTransaction.php');
require_once (dirname(__FILE__) . '/beGateway/GatewayTransport.php');
require_once (dirname(__FILE__) . '/beGateway/Authorization.php');
require_once (dirname(__FILE__) . '/beGateway/Payment.php');
require_once (dirname(__FILE__) . '/beGateway/Capture.php');
require_once (dirname(__FILE__) . '/beGateway/Void.php');
require_once (dirname(__FILE__) . '/beGateway/Refund.php');
require_once (dirname(__FILE__) . '/beGateway/Credit.php');
require_once (dirname(__FILE__) . '/beGateway/QueryByUid.php');
require_once (dirname(__FILE__) . '/beGateway/QueryByTrackingId.php');
require_once (dirname(__FILE__) . '/beGateway/QueryByToken.php');
require_once (dirname(__FILE__) . '/beGateway/GetPaymentPageToken.php');
require_once (dirname(__FILE__) . '/beGateway/Webhook.php');
require_once (dirname(__FILE__) . '/beGateway/CardToken.php');
?>
