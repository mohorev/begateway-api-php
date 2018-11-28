# BeGateway payment system API integration library

[![Build Status Master](https://travis-ci.org/begateway/begateway-api-php.svg?branch=master)](https://travis-ci.org/begateway/begateway-api-php)

## Requirements

PHP 5.6+

## Test data

### Shop without 3-D Secure

  * Shop Id __361__
  * Shop secret key __b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d__

### Shop with 3-D Secure

  * Shop Id __362__
  * Shop secret key __9ad8ad735945919845b9a1996af72d886ab43d3375502256dbf8dd16bca59a4e__

### Test data set

  * Card name __John Doe__
  * Card expiry month __01__ to get a success payment
  * Card expiry month __10__ to get a failed payment
  * CVC __123__

### Test card numbers

Refer to the documentation https://doc.begateway.com/test-integration#test-card-number for valid test card numbers.

## Getting started

### Setup

Before to use the library classes you must configure it.
You have to setup values of variables as follows:

  * `shopId`
  * `shopKey`
  * `gatewayBase`
  * `checkoutBase`

This data you will receive from your payment processor.

```php
\BeGateway\Settings::$shopId  = 361;
\BeGateway\Settings::$shopKey = 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d';
\BeGateway\Settings::$gatewayBase = 'https://demo-gateway.begateway.com';
\BeGateway\Settings::$checkoutBase = 'https://checkout.begateway.com';
```

### Hosted payment page

Simple usage looks like:

```php
use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\GetPaymentToken;

\BeGateway\Settings::$shopId  = 361;
\BeGateway\Settings::$shopKey = 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::INFO);

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com');
$customer->setAddress($address);
$customer->setIP('127.0.0.1');

$transaction = new GetPaymentToken($money, $customer);

$transaction->setDescription('test');
$transaction->setTrackingId('my_custom_variable');
$transaction->setLanguage('en');
$transaction->setNotificationUrl('http://www.example.com/notify');
$transaction->setSuccessUrl('http://www.example.com/success');
$transaction->setDeclineUrl('http://www.example.com/decline');
$transaction->setFailUrl('http://www.example.com/fail');
$transaction->setCancelUrl('http://www.example.com/cancel');

$response = (new ApiClient)->send($transaction);

if ($response->isSuccess()) {
  header("Location: " . $response->getRedirectUrl());
}
```

### Payment request via direct API

Simple usage looks like:

```php
use BeGateway\ApiClient;
use BeGateway\Address;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;

\BeGateway\Settings::$shopId  = 361;
\BeGateway\Settings::$shopKey = 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d';

// TODO: Logger example
// Logger::getInstance()->setLogLevel(Logger::INFO);

$card = new CreditCard('4200000000000000', 'John Doe', 1, 2030, '123');

$money = new Money(100, 'EUR'); // 1 EUR

$address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

$customer = new Customer('John', 'Doe', 'john@example.com');
$customer->setAddress($address);
$customer->setIP('127.0.0.1');

$transaction = new PaymentOperation($card, $money, $customer);
$transaction->setDescription('test order');
$transaction->setTrackingId('my_custom_variable');

$response = (new ApiClient)->send($transaction);

if ($response->isSuccess()) {
  print 'Status: ' . $response->getStatus() . PHP_EOL;
  print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
} elseif ($response->isFailed()) {
  print 'Status: ' . $response->getStatus() . PHP_EOL;
  print 'Transaction UID: ' . $response->getUid() . PHP_EOL;
  print 'Reason: ' . $response->getMessage() . PHP_EOL;
} else {
  print 'Status: error' . PHP_EOL;
  print 'Reason: ' . $response->getMessage() . PHP_EOL;
}
```

## Examples

See the [examples](docs/examples) directory for integration examples of different
transactions.

## Documentation

Visit https://doc.begateway.com for up-to-date documentation.

## Tests

To run tests

```bash
./vendor/bin/phpunit
```
