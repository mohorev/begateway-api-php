<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\PaymentMethod;
use BeGateway\Request\GetPaymentToken;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class GetPaymentTokenTest extends TestCase
{
    public function testCreate()
    {
        $request = new GetPaymentToken;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(GetPaymentToken::class, $request);
    }

    public function testGetSetDescription()
    {
        $request = $this->getTestRequest();

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertSame($description, $request->getDescription());
    }

    public function testGetSetTrackingId()
    {
        $request = $this->getTestRequest();

        $trackingId = 'test_tracking_id';
        $request->setTrackingId($trackingId);
        $this->assertSame($trackingId, $request->getTrackingId());
    }

    public function testGetSetExpiryDate()
    {
        $request = $this->getTestRequest();

        $date = date(DATE_ISO8601, strtotime('2020-12-30 23:21:46'));
        $request->setExpiryDate($date);
        $this->assertSame($date, $request->getExpiryDate());

        $date = null;
        $request->setExpiryDate($date);
        $this->assertSame(null, $request->getExpiryDate());
    }

    public function testGetSetUrls()
    {
        $request = $this->getTestRequest();

        $url = 'http://www.example.com';

        $request->setNotificationUrl($url . '/n');
        $request->setCancelUrl($url . '/c');
        $request->setSuccessUrl($url . '/s');
        $request->setDeclineUrl($url . '/d');
        $request->setFailUrl($url . '/f');

        $this->assertSame($url . '/n', $request->getNotificationUrl());
        $this->assertSame($url . '/c', $request->getCancelUrl());
        $this->assertSame($url . '/s', $request->getSuccessUrl());
        $this->assertSame($url . '/d', $request->getDeclineUrl());
        $this->assertSame($url . '/f', $request->getFailUrl());
    }

    public function testReadonlyFields()
    {
        $request = $this->getTestRequest();

        $request->setFirstNameReadonly();
        $request->setLastNameReadonly();
        $request->setEmailReadonly();
        $request->setCityReadonly();

        $this->assertSame(['first_name', 'last_name', 'email', 'city'], $request->getReadOnlyFields());

        $request->unsetFirstNameReadonly();

        $this->assertSame(['last_name', 'email', 'city'], $request->getReadOnlyFields());
    }

    public function testVisibleFields()
    {
        $request = $this->getTestRequest();
        $request->setPhoneVisible();
        $request->setAddressVisible();

        $this->assertSame(['phone', 'address'], $request->getVisibleFields());

        $request->unsetAddressVisible();

        $this->assertSame(['phone'], $request->getVisibleFields());
    }

    public function testGetSetTransactionType()
    {
        $request = $this->getTestRequest();
        $this->assertSame('payment', $request->getTransactionType());

        $request->setAuthorizationTransactionType();
        $this->assertSame('authorization', $request->getTransactionType());

        $request->setPaymentTransactionType();
        $this->assertSame('payment', $request->getTransactionType());

        $request->setTokenizationTransactionType();
        $this->assertSame('tokenization', $request->getTransactionType());
    }

    public function testGetSetTestMode()
    {
        $request = $this->getTestRequest();

        $this->assertTrue($request->getTestMode());

        $request->setTestMode(false);
        $this->assertFalse($request->getTestMode());

        $request->setTestMode(true);
        $this->assertTrue($request->getTestMode());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$checkoutBase . '/ctp/api/checkouts', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
            'checkout' => [
                'version' => '2.1',
                'transaction_type' => 'payment',
                'test' => true,
                'order' => [
                    'amount' => 1233,
                    'currency' => 'EUR',
                    'description' => 'test',
                    'tracking_id' => 'my_custom_variable',
                    'expired_at' => '2030-12-30T21:21:46+0000',
                    'additional_data' => [
                        'receipt_text' => [],
                        'contract' => [],
                    ],
                ],
                'settings' => [
                    'notification_url' => 'http://www.example.com/n',
                    'success_url' => 'http://www.example.com/s',
                    'decline_url' => 'http://www.example.com/d',
                    'cancel_url' => 'http://www.example.com/c',
                    'fail_url' => 'http://www.example.com/f',
                    'language' => 'zh',
                    'customer_fields' => [
                        'read_only' => [],
                        'visible' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => null,
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => null,
                    'birth_date' => null,
                ],
            ],
        ];

        $this->assertSame($expected, $request->data());

        $request->setTestMode(false);

        $expected['checkout']['test'] = false;
        $this->assertSame($expected, $request->data());
    }

    public function testDataWithErip()
    {
        $request = $this->getTestRequest();
        $request->money = new Money(100, 'BYN');

        $erip = new PaymentMethod\Erip([
            'account_number' => '1234',
            'service_no' => '99999999',
            'order_id' => 100001,
            'service_info' => ['Test payment'],
        ]);
        $cc = new PaymentMethod\CreditCard();

        $request->addPaymentMethod($erip);
        $request->addPaymentMethod($cc);

        $expected = [
            'checkout' => [
                'version' => '2.1',
                'transaction_type' => 'payment',
                'test' => true,
                'order' => [
                    'amount' => 100,
                    'currency' => 'BYN',
                    'description' => 'test',
                    'tracking_id' => 'my_custom_variable',
                    'expired_at' => '2030-12-30T21:21:46+0000',
                    'additional_data' => [
                        'receipt_text' => [],
                        'contract' => [],
                    ],
                ],
                'settings' => [
                    'notification_url' => 'http://www.example.com/n',
                    'success_url' => 'http://www.example.com/s',
                    'decline_url' => 'http://www.example.com/d',
                    'cancel_url' => 'http://www.example.com/c',
                    'fail_url' => 'http://www.example.com/f',
                    'language' => 'zh',
                    'customer_fields' => [
                        'read_only' => [],
                        'visible' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => null,
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => null,
                    'birth_date' => null,
                ],
                'payment_method' => [
                    'types' => ['erip', 'credit_card'],
                    'erip' => [
                        'order_id' => 100001,
                        'account_number' => '1234',
                        'service_no' => '99999999',
                        'service_info' => ['Test payment'],
                    ],
                    'credit_card' => [],
                ],
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testDataWithEmexVoucher()
    {
        $request = $this->getTestRequest();
        $request->money = new Money(100, 'USD');

        $emexVoucher = new PaymentMethod\EmexVoucher();
        $cc = new PaymentMethod\CreditCard();

        $request->addPaymentMethod($emexVoucher);
        $request->addPaymentMethod($cc);

        $expected = [
            'checkout' => [
                'version' => '2.1',
                'transaction_type' => 'payment',
                'test' => true,
                'order' => [
                    'amount' => 100,
                    'currency' => 'USD',
                    'description' => 'test',
                    'tracking_id' => 'my_custom_variable',
                    'expired_at' => '2030-12-30T21:21:46+0000',
                    'additional_data' => [
                        'receipt_text' => [],
                        'contract' => [],
                    ],
                ],
                'settings' => [
                    'notification_url' => 'http://www.example.com/n',
                    'success_url' => 'http://www.example.com/s',
                    'decline_url' => 'http://www.example.com/d',
                    'cancel_url' => 'http://www.example.com/c',
                    'fail_url' => 'http://www.example.com/f',
                    'language' => 'zh',
                    'customer_fields' => [
                        'read_only' => [],
                        'visible' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => null,
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => null,
                    'birth_date' => null,
                ],
                'payment_method' => [
                    'types' => ['emexvoucher', 'credit_card'],
                    'emexvoucher' => [],
                    'credit_card' => [],
                ],
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testRedirectUrl()
    {
        $request = $this->getTestRequest();

        $amount = mt_rand(0, 10000);

        $request->money = new Money($amount, 'EUR');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getToken());
        $this->assertNotNull($response->getRedirectUrl());
        $this->assertSame(
            Settings::$checkoutBase . '/v2/checkout?token=' . $response->getToken(),
            $response->getRedirectUrl()
        );
        $this->assertSame(
            Settings::$checkoutBase . '/v2/checkout',
            $response->getRedirectUrlScriptName()
        );
    }

    public function testSuccessTokenRequest()
    {
        $request = $this->getTestRequest();

        $amount = mt_rand(0, 10000);

        $request->money = new Money($amount, 'EUR');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getToken());
    }

    public function testErrorTokenRequest()
    {
        $request = $this->getTestRequest();

        $request->money = new Money(0, 'EUR');
        $request->setDescription('');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('description must be filled', $response->getMessage());
    }

    private function getTestRequest()
    {
        $this->authorize();

        $request = new GetPaymentToken;

        $url = 'http://www.example.com';

        $request->money = new Money(1233, 'EUR');

        $request->setPaymentTransactionType();
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setNotificationUrl($url . '/n');
        $request->setCancelUrl($url . '/c');
        $request->setSuccessUrl($url . '/s');
        $request->setDeclineUrl($url . '/d');
        $request->setFailUrl($url . '/f');
        $request->setLanguage('zh');
        $request->setExpiryDate('2030-12-31T00:21:46+0300');
        $request->setTestMode(true);

        $request->customer->setFirstName('John');
        $request->customer->setLastName('Doe');
        $request->customer->setCountry('LV');
        $request->customer->setAddress('Demo str 12');
        $request->customer->setCity('Riga');
        $request->customer->setZip('LV-1082');
        $request->customer->setIp('127.0.0.1');
        $request->customer->setEmail('john@example.com');

        return $request;
    }
}
