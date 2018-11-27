<?php

use BeGateway\ApiClient;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CaptureOperation;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$money = new Money(100, 'EUR'); // 1 EUR

$transaction = new AuthorizationOperation($money);

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

$response = (new ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Capture transaction ' . $response->getUid() . PHP_EOL;

    $capture = new CaptureOperation;
    $capture->setParentUid($response->getUid());
    $capture->money = $transaction->money;

    $response = (new ApiClient)->send($capture);

    if ($response->isSuccess()) {
        print 'Captured successfully. Captured transaction UID ' . $response->getUid() . PHP_EOL;
    } else {
        print 'Problem to capture' . PHP_EOL;
        print 'Capture message: ' . $response->getMessage() . PHP_EOL;
    }
}
