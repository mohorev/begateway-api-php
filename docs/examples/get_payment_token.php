<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\GetPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com');
$customer->setAddress($address);
$customer->setIP('127.0.0.1');

$transaction = new GetPaymentToken($money, $customer);
$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$transaction->setNotificationUrl('http://www.example.com/notify');
$transaction->setSuccessUrl('http://www.example.com/success');
$transaction->setDeclineUrl('http://www.example.com/decline');
$transaction->setFailUrl('http://www.example.com/fail');
$transaction->setCancelUrl('http://www.example.com/cancel');

// set transaction type. Default - payment
// $transaction->setPaymentTransactionType();
// $transaction->setAuthorizationTransactionType();
// $transaction->setTokenizationTransactionType();

$client = new ApiClient([
    'language' => 'en',
    'test' => true,
]);
$response = $client->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Token: ' . $response->getToken() . PHP_EOL;
}
