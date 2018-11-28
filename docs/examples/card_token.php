<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CardToken;
use BeGateway\Request\PaymentOperation;
use BeGateway\TokenCard;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$token = new CardToken('4200000000000000', 'John Doe', 1, 2029);

$response = (new ApiClient)->send($token);

if ($response->isSuccess()) {
    print 'Card token: ' . $response->token . PHP_EOL;
    print 'Trying to make a payment by the token and with CVC 123' . PHP_EOL;

    $money = new Money(100, 'EUR'); // 1 EUR

    $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

    $customer = new Customer('John', 'Doe', 'john@example.com');
    $customer->setAddress($address);
    $customer->setIP('127.0.0.1');

    $card = new TokenCard($response->token);

//    TODO: Payment operation with CVC
//    $transaction->card->setCardCvc('123');
//    $transaction->card->setCardToken();

    $transaction = new PaymentOperation($card, $money, $customer);
    $transaction->setDescription('test');
    $transaction->setTrackingId('my_custom_variable');
    $transaction->setTestMode(true);

    $response = (new ApiClient)->send($transaction);

    print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
    print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

    if ($response->isSuccess()) {
        print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    }
}
