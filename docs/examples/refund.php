<?php

require_once __DIR__ . '/../../src/BeGateway.php';
require_once __DIR__ . '/test_shop_data.php';

\BeGateway\Logger::getInstance()->setLogLevel(\BeGateway\Logger::DEBUG);

$transaction = new \BeGateway\Request\PaymentOperation;

$amount = rand(1, 100);

$transaction->money->setAmount($amount);
$transaction->money->setCurrency('EUR');
$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$transaction->setTestMode(true);

$transaction->card->setCardNumber('4200000000000000');
$transaction->card->setCardHolder('JOHN DOE');
$transaction->card->setCardExpMonth(1);
$transaction->card->setCardExpYear(2030);
$transaction->card->setCardCvc('123');

$transaction->customer->setFirstName('John');
$transaction->customer->setLastName('Doe');
$transaction->customer->setCountry('LV');
$transaction->customer->setAddress('Demo str 12');
$transaction->customer->setCity('Riga');
$transaction->customer->setZip('LV-1082');
$transaction->customer->setIp('127.0.0.1');
$transaction->customer->setEmail('john@example.com');

$response = (new \BeGateway\ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Refund transaction ' . $response->getUid() . PHP_EOL;

    $refund = new \BeGateway\Request\RefundOperation;
    $refund->setParentUid($response->getUid());
    $refund->money->setAmount($transaction->money->getAmount());
    $refund->setReason('customer request');

    $response = (new \BeGateway\ApiClient)->send($refund);

    if ($response->isSuccess()) {
        print 'Refund successfully. Refund transaction UID ' . $response->getUid() . PHP_EOL;
    } else {
        print 'Problem to refund' . PHP_EOL;
        print 'Refund message: ' . $response->getMessage() . PHP_EOL;
    }
}
