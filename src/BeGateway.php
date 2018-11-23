<?php

if (!function_exists('curl_init')) {
    throw new Exception('BeGateway needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('BeGateway needs the JSON PHP extension.');
}

if (!class_exists('\BeGateway\Settings')) {
    require_once(dirname(__FILE__) . '/../vendor/psr/log/Psr/Log/LoggerAwareInterface.php');
    require_once(dirname(__FILE__) . '/../vendor/psr/log/Psr/Log/LoggerAwareTrait.php');

    require_once (__DIR__ . '/BeGateway/ApiClient.php');

    require_once (__DIR__ . '/BeGateway/Settings.php');
    require_once (__DIR__ . '/BeGateway/Logger.php');
    require_once (__DIR__ . '/BeGateway/Language.php');
    require_once (__DIR__ . '/BeGateway/Customer.php');
    require_once (__DIR__ . '/BeGateway/AdditionalData.php');
    require_once (__DIR__ . '/BeGateway/Card.php');
    require_once (__DIR__ . '/BeGateway/Money.php');
    require_once (__DIR__ . '/BeGateway/GatewayTransport.php');
    require_once (__DIR__ . '/BeGateway/AdditionalData.php');

    require_once (__DIR__ . '/BeGateway/Contract/Request.php');
    require_once (__DIR__ . '/BeGateway/Contract/Response.php');
    require_once (__DIR__ . '/BeGateway/Contract/GatewayTransport.php');
    require_once (__DIR__ . '/BeGateway/Contract/PaymentMethod.php');

    require_once (__DIR__ . '/BeGateway/PaymentMethod/Erip.php');
    require_once (__DIR__ . '/BeGateway/PaymentMethod/CreditCard.php');
    require_once (__DIR__ . '/BeGateway/PaymentMethod/CreditCardHalva.php');
    require_once (__DIR__ . '/BeGateway/PaymentMethod/EmexVoucher.php');

    require_once (__DIR__ . '/BeGateway/Request/BaseRequest.php');
    require_once (__DIR__ . '/BeGateway/Request/AuthorizationOperation.php');
    require_once (__DIR__ . '/BeGateway/Request/CreditOperation.php');
    require_once (__DIR__ . '/BeGateway/Request/ChildTransaction.php');
    require_once (__DIR__ . '/BeGateway/Request/CardToken.php');
    require_once (__DIR__ . '/BeGateway/Request/QueryByUid.php');
    require_once (__DIR__ . '/BeGateway/Request/QueryByTrackingId.php');
    require_once (__DIR__ . '/BeGateway/Request/QueryByPaymentToken.php');
    require_once (__DIR__ . '/BeGateway/Request/GetPaymentToken.php');
    require_once (__DIR__ . '/BeGateway/Request/PaymentOperation.php');
    require_once (__DIR__ . '/BeGateway/Request/CaptureOperation.php');
    require_once (__DIR__ . '/BeGateway/Request/VoidOperation.php');
    require_once (__DIR__ . '/BeGateway/Request/RefundOperation.php');

    require_once (__DIR__ . '/BeGateway/Response/BaseResponse.php');
    require_once (__DIR__ . '/BeGateway/Response/CardTokenResponse.php');
    require_once (__DIR__ . '/BeGateway/Response/CheckoutResponse.php');
    require_once (__DIR__ . '/BeGateway/Response/TransactionResponse.php');
    require_once (__DIR__ . '/BeGateway/Response/WebhookResponse.php');

    require_once (__DIR__ . '/BeGateway/Transport/CurlTransport.php');
}
