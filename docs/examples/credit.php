<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;
use BeGateway\Token;

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

$transaction = new PaymentOperation($card, $money, $customer);
$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$response = $client->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Credit to card ' . $transaction->getCard()->getNumber() . PHP_EOL;

    $money = new Money(3000, 'EUR'); // 30 EUR
    $token = new Token($response->getResponse()->transaction->credit_card->token);

    $credit = new CreditOperation($money, $token, 'tracking_id');
    $credit->setDescription('Test credit');

    $response = $client->send($credit);

    if ($response->isSuccess()) {
        print 'Credited successfully. Credit transaction UID ' . $response->getUid() . PHP_EOL;
    } else {
        print 'Problem to credit' . PHP_EOL;
        print 'Credit message: ' . $response->getMessage() . PHP_EOL;
    }
}
