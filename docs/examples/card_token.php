<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CardToken;
use BeGateway\Request\PaymentOperation;
use BeGateway\TokenCard;

require_once __DIR__ . '/test_shop_data.php';

$token = new CardToken('4200000000000000', 'John Doe', 1, 2029);

$client = new ApiClient([
    'shop_id' => 361,
    'shop_key' => 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d',
    'language' => 'en',
    'test' => true,
]);

$response = $client->send($token);

if ($response->isSuccess()) {
    print 'Card token: ' . $response->token . PHP_EOL;
    print 'Trying to make a payment by the token and with CVC 123' . PHP_EOL;

    $money = new Money(100, 'EUR'); // 1 EUR

    $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

    $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
    $customer->setAddress($address);

    $card = new TokenCard($response->token);

//    TODO: Payment operation with CVC
//    $transaction->card->setCardCvc('123');
//    $transaction->card->setCardToken();

    $transaction = new PaymentOperation($card, $money, $customer);
    $transaction->setDescription('test');
    $transaction->setTrackingId('my_custom_variable');

    $response = $client->send($transaction);

    print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
    print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

    if ($response->isSuccess()) {
        print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    }
}
