<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;
use BeGateway\Request\QueryByTrackingId;

require_once __DIR__ . '/test_shop_data.php';

$client = new ApiClient([
    'shop_id' => 361,
    'shop_key' => 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d',
    'language' => 'en',
    'test' => true,
]);

$card = new Card('4200000000000000', 'JOHN DOE', 1, 2030, '123');

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
$customer->setAddress($address);

$transaction = new PaymentOperation($card, $money, $customer, 'tracking_id');
$transaction->setDescription('test');

$response = $client->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Query by tracking id ' . $transaction->getTrackingId() . PHP_EOL;

    $query = new QueryByTrackingId($transaction->getTrackingId());

    $response = $client->send($query);

    print_r($response);
}
