<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;

require_once __DIR__ . '/test_shop_data.php';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::DEBUG);

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com');
$customer->setAddress($address);
$customer->setIP('127.0.0.1');

$transaction = new PaymentOperation($money, $customer);

$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');

$transaction->setTestMode(true);

$transaction->card->setCardNumber('4200000000000000');
$transaction->card->setCardHolder('JOHN DOE');
$transaction->card->setCardExpMonth(1);
$transaction->card->setCardExpYear(2029);
$transaction->card->setCardCvc('123');

$response = (new ApiClient)->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;
print 'Transaction status: ' . $response->getStatus() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
    print 'Trying to Credit to card ' . $transaction->card->getCardNumber() . PHP_EOL;

    $money = new Money(3000, 'EUR'); // 30 EUR

    $credit = new CreditOperation($money);

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
