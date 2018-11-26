<?php

use BeGateway\ApiClient;
use BeGateway\PaymentMethod\CreditCard;
use BeGateway\PaymentMethod\Erip;
use BeGateway\Request\GetPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$transaction = new GetPaymentToken;

$cc = new CreditCard;
$erip = new Erip([
    'order_id' => 1234,
    'account_number' => '1234',
    'service_no' => '99999999',
    'service_info' => ['Order 1234'],
]);

$transaction->addPaymentMethod($cc);
$transaction->addPaymentMethod($erip);

$amount = rand(100, 1000);

$transaction->money->setAmount($amount);
$transaction->money->setCurrency('BYN');
$transaction->setDescription('Тестовая оплата');
$transaction->setTrackingId('my_custom_variable');
$transaction->setLanguage('ru');

$transaction->setTestMode(true);

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
