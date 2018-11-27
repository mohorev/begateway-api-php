<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CardToken;
use BeGateway\Request\PaymentOperation;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$token = new CardToken;
$token->card->setCardNumber('4200000000000000');
$token->card->setCardHolder('John Doe');
$token->card->setCardExpMonth(1);
$token->card->setCardExpYear(2029);

$response = (new ApiClient)->send($token);

if ($response->isSuccess()) {
    print 'Card token: ' . $response->card->getCardToken() . PHP_EOL;
    print 'Trying to make a payment by the token and with CVC 123' . PHP_EOL;

    $money = new Money(100, 'EUR'); // 1 EUR

    $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

    $customer = new Customer('John', 'Doe', 'john@example.com');
    $customer->setAddress($address);
    $customer->setIP('127.0.0.1');

    $transaction = new PaymentOperation($money, $customer);

    $transaction->setDescription('test');
    $transaction->setTrackingId('my_custom_variable');

    $transaction->card->setCardCvc('123');
    $transaction->card->setCardToken($response->card->getCardToken());

    $transaction->setTestMode(true);

    $response = (new ApiClient)->send($transaction);

    print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
    print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

    if ($response->isSuccess()) {
        print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    }
}
