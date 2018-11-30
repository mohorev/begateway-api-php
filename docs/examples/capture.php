<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CaptureOperation;

require_once __DIR__ . '/test_shop_data.php';

$card = new CreditCard('4200000000000000', 'JOHN DOE', 1, 2030, '123');

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com');
$customer->setAddress($address);
$customer->setIP('127.0.0.1');

$transaction = new AuthorizationOperation($card, $money, $customer);
$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$client = new ApiClient([
    'language' => 'en',
    'test' => true,
]);

$response = $client->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Capture transaction ' . $response->getUid() . PHP_EOL;

    $capture = new CaptureOperation($transaction->money);
    $capture->setParentUid($response->getUid());

    $response = $client->send($capture);

    if ($response->isSuccess()) {
        print 'Captured successfully. Captured transaction UID ' . $response->getUid() . PHP_EOL;
    } else {
        print 'Problem to capture' . PHP_EOL;
        print 'Capture message: ' . $response->getMessage() . PHP_EOL;
    }
}
