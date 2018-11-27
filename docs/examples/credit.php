<?php

use BeGateway\ApiClient;
use BeGateway\Money;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$transaction = new PaymentOperation;

$transaction->money = new Money(100, 'EUR'); // 1 EUR

$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$transaction->setTestMode(true);

$transaction->card->setCardNumber('4200000000000000');
$transaction->card->setCardHolder('JOHN DOE');
$transaction->card->setCardExpMonth(1);
$transaction->card->setCardExpYear(2029);
$transaction->card->setCardCvc('123');

$transaction->customer->setFirstName('John');
$transaction->customer->setLastName('Doe');
$transaction->customer->setCountry('LV');
$transaction->customer->setAddress('Demo str 12');
$transaction->customer->setCity('Riga');
$transaction->customer->setZip('LV-1082');
$transaction->customer->setIp('127.0.0.1');
$transaction->customer->setEmail('john@example.com');

$response = (new ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Credit to card ' . $transaction->card->getCardNumber() . PHP_EOL;

    $credit = new CreditOperation;

    $credit->money = new Money(3000, 'EUR'); // 30 EUR

    $credit->card->setCardToken($response->getResponse()->transaction->credit_card->token);
    $credit->setDescription('Test credit');

    $response = (new ApiClient)->send($credit);

    if ($response->isSuccess()) {
        print 'Credited successfully. Credit transaction UID ' . $response->getUid() . PHP_EOL;
    } else {
        print 'Problem to credit' . PHP_EOL;
        print 'Credit message: ' . $response->getMessage() . PHP_EOL;
    }
}
