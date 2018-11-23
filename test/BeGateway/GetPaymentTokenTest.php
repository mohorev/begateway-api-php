<?php

namespace BeGateway;

use BeGateway\Request\GetPaymentToken;

class GetPaymentTokenTest extends TestCase
{
    public function test_setDescription()
    {
        $request = $this->getTestObjectInstance();

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertEqual($request->getDescription(), $description);
    }

    public function test_setTrackingId()
    {
        $request = $this->getTestObjectInstance();

        $trackingId = 'Test tracking_id';
        $request->setTrackingId($trackingId);
        $this->assertEqual($request->getTrackingId(), $trackingId);
    }

    public function test_setExpiryDate()
    {
        $request = $this->getTestObjectInstance();

        $date = date(DATE_ISO8601, strtotime('2020-12-30 23:21:46'));
        $request->setExpiryDate($date);
        $this->assertEqual($request->getExpiryDate(), $date);

        $date = null;
        $request->setExpiryDate($date);
        $this->assertEqual($request->getExpiryDate(), null);
    }

    public function test_setUrls()
    {
        $request = $this->getTestObjectInstance();

        $url = 'http://www.example.com';

        $request->setNotificationUrl($url . '/n');
        $request->setCancelUrl($url . '/c');
        $request->setSuccessUrl($url . '/s');
        $request->setDeclineUrl($url . '/d');
        $request->setFailUrl($url . '/f');

        $this->assertEqual($request->getNotificationUrl(), $url . '/n');
        $this->assertEqual($request->getCancelUrl(), $url . '/c');
        $this->assertEqual($request->getSuccessUrl(), $url . '/s');
        $this->assertEqual($request->getDeclineUrl(), $url . '/d');
        $this->assertEqual($request->getFailUrl(), $url . '/f');
    }

    public function test_readonly()
    {
        $request = $this->getTestObjectInstance();

        $request->setFirstNameReadonly();
        $request->setLastNameReadonly();
        $request->setEmailReadonly();
        $request->setCityReadonly();

        $this->assertEqual(array_diff($request->getReadOnlyFields(), ['first_name', 'last_name', 'email', 'city']), []);

        $request->unsetFirstNameReadonly();

        $this->assertEqual(array_diff($request->getReadOnlyFields(), ['last_name', 'email', 'city']), []);

    }

    public function test_visible()
    {
        $request = $this->getTestObjectInstance();
        $request->setPhoneVisible();
        $request->setAddressVisible();

        $this->assertEqual(array_diff($request->getVisibleFields(), ['phone', 'address']), []);

        $request->unsetAddressVisible();

        $this->assertEqual(array_diff($request->getVisibleFields(), ['phone']), []);
    }

    public function test_transaction_type()
    {
        $request = $this->getTestObjectInstance();
        $request->setAuthorizationTransactionType();

        $this->assertEqual($request->getTransactionType(), 'authorization');
    }

    public function test_setTestMode()
    {
        $request = $this->getTestObjectInstance();
        $this->assertFalse($request->getTestMode());
        $request->setTestMode(true);
        $this->assertTrue($request->getTestMode());
        $request->setTestMode(false);
        $this->assertFalse($request->getTestMode());
    }

    public function test_buildRequestMessage()
    {
        $request = $this->getTestObject();

        $arr = [
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
                    'success_url' => 'http://www.example.com/s',
                    'cancel_url' => 'http://www.example.com/c',
                    'decline_url' => 'http://www.example.com/d',
                    'fail_url' => 'http://www.example.com/f',
                    'notification_url' => 'http://www.example.com/n',
                    'language' => 'zh',
                    'customer_fields' => [
                        'visible' => [],
                        'read_only' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => '',
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => '',
                    'birth_date' => '',
                ],
            ],
        ];

        $this->assertEqual($arr, $request->data());

        $arr['checkout']['test'] = false;
        $request->setTestMode(false);

        $this->assertEqual($arr, $request->data());
    }

    public function test_buildRequestMessageWithErip()
    {
        $request = $this->getTestObject();
        $request->money->setAmount(100);
        $request->money->setCurrency('BYN');

        $erip = new PaymentMethod\Erip([
            'account_number' => '1234',
            'service_no' => '99999999',
            'order_id' => 100001,
            'service_info' => ['Test payment'],
        ]);
        $cc = new PaymentMethod\CreditCard();

        $request->addPaymentMethod($erip);
        $request->addPaymentMethod($cc);

        $arr = [
            'checkout' => [
                'version' => "2.1",
                'transaction_type' => 'payment',
                'test' => true,
                'order' => [
                    'amount' => 10000,
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
                    'success_url' => 'http://www.example.com/s',
                    'cancel_url' => 'http://www.example.com/c',
                    'decline_url' => 'http://www.example.com/d',
                    'fail_url' => 'http://www.example.com/f',
                    'notification_url' => 'http://www.example.com/n',
                    'language' => 'zh',
                    'customer_fields' => [
                        'visible' => [],
                        'read_only' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => '',
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => '',
                    'birth_date' => null,
                ],
                'payment_method' => [
                    'types' => ['erip', 'credit_card'],
                    'erip' => [
                        'account_number' => '1234',
                        'service_no' => '99999999',
                        'order_id' => 100001,
                        'service_info' => ['Test payment'],
                    ],
                    'credit_card' => [],
                ],
            ],
        ];

        $this->assertEqual($arr, $request->data());
    }

    public function test_buildRequestMessageWithEmexVoucher()
    {
        $request = $this->getTestObject();
        $request->money->setAmount(100);
        $request->money->setCurrency('USD');

        $emexVoucher = new PaymentMethod\EmexVoucher();
        $cc = new PaymentMethod\CreditCard();

        $request->addPaymentMethod($emexVoucher);
        $request->addPaymentMethod($cc);

        $arr = [
            'checkout' => [
                'version' => '2.1',
                'transaction_type' => 'payment',
                'test' => true,
                'order' => [
                    'amount' => 10000,
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
                    'success_url' => 'http://www.example.com/s',
                    'cancel_url' => 'http://www.example.com/c',
                    'decline_url' => 'http://www.example.com/d',
                    'fail_url' => 'http://www.example.com/f',
                    'notification_url' => 'http://www.example.com/n',
                    'language' => 'zh',
                    'customer_fields' => [
                        'visible' => [],
                        'read_only' => [],
                    ],
                ],
                'customer' => [
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => '',
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => '',
                    'birth_date' => null,
                ],
                'payment_method' => [
                    'types' => ['emexvoucher', 'credit_card'],
                    'credit_card' => [],
                    'emexvoucher' => [],
                ],
            ],
        ];

        $this->assertEqual($arr, $request->data());
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();

        $this->assertEqual($request->endpoint(), Settings::$checkoutBase . '/ctp/api/checkouts');
    }

    public function test_successTokenRequest()
    {
        $request = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getToken());
    }

    public function test_redirectUrl()
    {
        $request = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getToken());
        $this->assertNotNull($response->getRedirectUrl());
        $this->assertEqual(
            \BeGateway\Settings::$checkoutBase . '/v2/checkout?token=' . $response->getToken(),
            $response->getRedirectUrl()
        );
        $this->assertEqual(
            \BeGateway\Settings::$checkoutBase . '/v2/checkout',
            $response->getRedirectUrlScriptName()
        );
    }

    public function test_errorTokenRequest()
    {
        $request = $this->getTestObject();

        $request->money->setAmount(0);
        $request->setDescription('');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
    }

    protected function getTestObject()
    {
        $request = $this->getTestObjectInstance();

        $url = 'http://www.example.com';

        $request->money->setAmount(12.33);
        $request->money->setCurrency('EUR');
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

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new GetPaymentToken();
    }
}
