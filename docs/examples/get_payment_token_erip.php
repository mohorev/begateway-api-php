<?php

use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\PaymentMethod\CreditCard;
use BeGateway\PaymentMethod\Erip;
use BeGateway\Request\GetPaymentToken;

require_once __DIR__ . '/test_shop_data.php';

$client = new ApiClient([
    'shop_id' => 361,
    'shop_key' => 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d',
    'language' => 'en',
    'test' => true,
]);

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
$customer->setAddress($address);

$transaction = new GetPaymentToken($money, $customer);

$transaction->addPaymentMethod(new CreditCard);
$transaction->addPaymentMethod(new Erip(1234, '1234', '99999999', ['Order 1234']));

$transaction->setDescription('Тестовая оплата');
$transaction->setTrackingId('my_custom_variable');

$transaction->setNotificationUrl('http://www.example.com/notify');
$transaction->setSuccessUrl('http://www.example.com/success');
$transaction->setDeclineUrl('http://www.example.com/decline');
$transaction->setFailUrl('http://www.example.com/fail');
$transaction->setCancelUrl('http://www.example.com/cancel');

# No available to make payment for the order in 2 days
$transaction->setExpiryDate(date('Y-m-d', 3 * 24 * 3600 + time()) . 'T00:00:00+03:00');

$response = $client->send($transaction);

print 'Transaction message: ' . $response->getMessage() . PHP_EOL;

if ($response->isSuccess()) {
    print 'Token: ' . $response->getToken() . PHP_EOL;
}
