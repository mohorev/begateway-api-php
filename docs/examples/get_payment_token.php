<?php

use BeGateway\ApiClient;
use BeGateway\Money;
use BeGateway\Request\GetPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$transaction = new GetPaymentToken;

$transaction->money = new Money(100, 'EUR'); // 1 EUR

$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');
$transaction->setLanguage('en');

$transaction->setTestMode(true);

$transaction->setNotificationUrl('http://www.example.com/notify');
$transaction->setSuccessUrl('http://www.example.com/success');
$transaction->setDeclineUrl('http://www.example.com/decline');
$transaction->setFailUrl('http://www.example.com/fail');
$transaction->setCancelUrl('http://www.example.com/cancel');

$transaction->customer->setFirstName('John');
$transaction->customer->setLastName('Doe');
$transaction->customer->setCountry('LV');
$transaction->customer->setAddress('Demo str 12');
$transaction->customer->setCity('Riga');
$transaction->customer->setZip('LV-1082');
$transaction->customer->setIp('127.0.0.1');
$transaction->customer->setEmail('john@example.com');
$transaction->customer->setBirthDate('1970-01-12');
// set transaction type. Default - payment
// $transaction->setPaymentTransactionType();
// $transaction->setAuthorizationTransactionType();
// $transaction->setTokenizationTransactionType();

$response = (new ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Token: ' . $response->getToken() . PHP_EOL;
}
