<?php

if (!function_exists('curl_init')) {
    throw new Exception('BeGateway needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('BeGateway needs the JSON PHP extension.');
}

if (!class_exists('\BeGateway\Settings')) {
    require_once (__DIR__ . '/BeGateway/Settings.php');
    require_once (__DIR__ . '/BeGateway/Logger.php');
    require_once (__DIR__ . '/BeGateway/Language.php');
    require_once (__DIR__ . '/BeGateway/Customer.php');
    require_once (__DIR__ . '/BeGateway/AdditionalData.php');
    require_once (__DIR__ . '/BeGateway/Card.php');
    require_once (__DIR__ . '/BeGateway/Money.php');
    require_once (__DIR__ . '/BeGateway/ResponseBase.php');
    require_once (__DIR__ . '/BeGateway/Response.php');
    require_once (__DIR__ . '/BeGateway/ResponseCheckout.php');
    require_once (__DIR__ . '/BeGateway/ResponseCardToken.php');
    require_once (__DIR__ . '/BeGateway/GatewayTransport.php');
    require_once (__DIR__ . '/BeGateway/AdditionalData.php');

    require_once (__DIR__ . '/BeGateway/Webhook.php');

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
}
