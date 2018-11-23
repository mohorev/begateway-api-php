<?php

namespace BeGateway;

use BeGateway\Request\PaymentOperation;

class PaymentOperationTest extends TestCase
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

    public function test_setNotificationUrl()
    {
        $request = $this->getTestObjectInstance();

        $url = 'http://www.example.com';
        $request->setNotificationUrl($url);
        $this->assertEqual($request->getNotificationUrl(), $url);
    }

    public function test_setReturnUrl()
    {
        $request = $this->getTestObjectInstance();

        $url = 'http://www.example.com';
        $request->setReturnUrl($url);
        $this->assertEqual($request->getReturnUrl(), $url);
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();

        $this->assertEqual($request->endpoint(), Settings::$gatewayBase . '/transactions/payments');
    }

    public function test_setTestMode()
    {
        $auth = $this->getTestObjectInstance();
        $this->assertFalse($auth->getTestMode());
        $auth->setTestMode(true);
        $this->assertTrue($auth->getTestMode());
        $auth->setTestMode(false);
        $this->assertFalse($auth->getTestMode());
    }

    public function test_buildRequestMessage()
    {
        $request = $this->getTestObject();
        $arr = [
            'request' => [
                'amount' => 1233,
                'currency' => 'EUR',
                'description' => 'test',
                'tracking_id' => 'my_custom_variable',
                'notification_url' => '',
                'return_url' => '',
                'language' => 'en',
                'test' => true,
                'credit_card' => [
                    'number' => '4200000000000000',
                    'verification_value' => '123',
                    'holder' => 'BEGATEWAY',
                    'exp_month' => '01',
                    'exp_year' => '2030',
                    'token' => '',
                    'skip_three_d_secure_verification' => '',
                ],
                'customer' => [
                    'ip' => '127.0.0.1',
                    'email' => 'john@example.com',
                    'birth_date' => '1970-01-01',
                ],
                'billing_address' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => '',
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => '',
                ],
                'additional_data' => [
                    'receipt_text' => [],
                    'contract' => [],
                ],
            ],
        ];

        $this->assertEqual($arr, $request->data());

        $arr['request']['test'] = false;
        $request->setTestMode(false);

        $this->assertEqual($arr, $request->data());
    }

    public function test_successPayment()
    {
        $request = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $cents = $request->money->getCents();

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertEqual($response->getMessage(), 'Successfully processed');
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getStatus(), 'successful');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);
    }

    public function test_incompletePayment()
    {
        $request = $this->getTestObject(true);

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $request->card->setCardNumber('4012001037141112');
        $cents = $request->money->getCents();

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isIncomplete());
        $this->assertFalse($response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertNotNull($response->getResponse()->transaction->redirect_url);
        $this->assertTrue(preg_match('/process/', $response->getResponse()->transaction->redirect_url));
        $this->assertEqual($response->getStatus(), 'incomplete');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);
    }

    public function test_failedPayment()
    {
        $request = $this->getTestObject();
        $request->card->setCardNumber('4005550000000019');

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $cents = $request->money->getCents();
        $request->card->setCardExpMonth(10);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isFailed());
        $this->assertEqual($response->getMessage(), 'Payment was declined');
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getStatus(), 'failed');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);
    }

    protected function getTestObject($threed = false)
    {
        $request = $this->getTestObjectInstance($threed);

        $request->money->setAmount(12.33);
        $request->money->setCurrency('EUR');
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setTestMode(true);

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('BEGATEWAY');
        $request->card->setCardExpMonth(1);
        $request->card->setCardExpYear(2030);
        $request->card->setCardCvc('123');

        $request->customer->setFirstName('John');
        $request->customer->setLastName('Doe');
        $request->customer->setCountry('LV');
        $request->customer->setAddress('Demo str 12');
        $request->customer->setCity('Riga');
        $request->customer->setZip('LV-1082');
        $request->customer->setIp('127.0.0.1');
        $request->customer->setEmail('john@example.com');
        $request->customer->setBirthDate('1970-01-01');

        return $request;
    }

    protected function getTestObjectInstance($threed = false)
    {
        self::authorizeFromEnv($threed);

        return new PaymentOperation();
    }
}
