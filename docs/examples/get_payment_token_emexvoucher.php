<?php

use BeGateway\ApiClient;
use BeGateway\Money;
use BeGateway\PaymentMethod\EmexVoucher;
use BeGateway\Request\GetPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$money = new Money(100, 'EUR'); // 1 EUR

$transaction = new GetPaymentToken($money);

$voucher = new EmexVoucher;

$transaction->addPaymentMethod($voucher);

$transaction->setDescription('Test payment');
$transaction->setTrackingId('my_custom_variable');
$transaction->setLanguage('en');
$transaction->setNotificationUrl('http://www.example.com/notify');
$transaction->setSuccessUrl('http://www.example.com/success');
$transaction->setDeclineUrl('http://www.example.com/decline');
$transaction->setFailUrl('http://www.example.com/fail');
$transaction->setCancelUrl('http://www.example.com/cancel');

# No available to make payment for the order in 2 days
$transaction->setExpiryDate(date('Y-m-d', 3 * 24 * 3600 + time()) . 'T00:00:00+03:00');

$transaction->customer->setEmail('john@example.com');

$response = (new ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Token: ' . $response->getToken() . PHP_EOL;
}
