<?php

namespace BeGateway;

class AuthorizationOperationTest extends TestCase
{
    public function test_setDescription()
    {
        $auth = $this->getTestObjectInstance();

        $description = 'Test description';

        $auth->setDescription($description);

        $this->assertEqual($auth->getDescription(), $description);
    }

    public function test_setTrackingId()
    {
        $auth = $this->getTestObjectInstance();
        $trackingId = 'Test tracking_id';
        $auth->setTrackingId($trackingId);
        $this->assertEqual($auth->getTrackingId(), $trackingId);
    }

    public function test_setNotificationUrl()
    {
        $auth = $this->getTestObjectInstance();
        $url = 'http://www.example.com';
        $auth->setNotificationUrl($url);
        $this->assertEqual($auth->getNotificationUrl(), $url);
    }

    public function test_setReturnUrl()
    {
        $auth = $this->getTestObjectInstance();
        $url = 'http://www.example.com';
        $auth->setReturnUrl($url);
        $this->assertEqual($auth->getReturnUrl(), $url);
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
        $auth = $this->getTestObject();
        $arr = [
            'request' => [
                'amount' => 1233,
                'currency' => 'EUR',
                'description' => 'test',
                'tracking_id' => 'my_custom_variable',
                'notification_url' => '',
                'return_url' => '',
                'language' => 'de',
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

        $reflection = new \ReflectionClass('BeGateway\AuthorizationOperation');
        $method = $reflection->getMethod('buildRequestMessage');
        $method->setAccessible(true);

        $request = $method->invoke($auth, 'buildRequestMessage');

        $this->assertEqual($arr, $request);

        $arr['request']['test'] = false;
        $auth->setTestMode(false);
        $request = $method->invoke($auth, 'buildRequestMessage');

        $this->assertEqual($arr, $request);
    }

    public function test_endpoint()
    {

        $auth = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\AuthorizationOperation');
        $method = $reflection->getMethod('endpoint');
        $method->setAccessible(true);
        $url = $method->invoke($auth, 'endpoint');

        $this->assertEqual($url, Settings::$gatewayBase . '/transactions/authorizations');
    }

    public function test_successAuthorization()
    {
        $auth = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $auth->money->setAmount($amount);
        $cents = $auth->money->getCents();

        $response = $auth->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertEqual($response->getMessage(), 'Successfully processed');
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getStatus(), 'successful');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);

        $arResponse = $response->getResponseArray();
        $this->assertEqual($cents, $arResponse['transaction']['amount']);
    }

    public function test_incompleteAuthorization()
    {
        $auth = $this->getTestObject(true);

        $amount = rand(0, 10000) / 100;

        $auth->money->setAmount($amount);
        $auth->card->setCardNumber('4012001037141112');
        $cents = $auth->money->getCents();

        $response = $auth->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isIncomplete());
        $this->assertFalse($response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertNotNull($response->getResponse()->transaction->redirect_url);
        $this->assertTrue(preg_match('/process/', $response->getResponse()->transaction->redirect_url));
        $this->assertEqual($response->getStatus(), 'incomplete');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);

        $arResponse = $response->getResponseArray();
        $this->assertEqual($cents, $arResponse['transaction']['amount']);
    }

    public function test_failedAuthorization()
    {
        $auth = $this->getTestObject();
        $auth->card->setCardNumber('4005550000000019');

        $amount = rand(0, 10000) / 100;

        $auth->money->setAmount($amount);
        $cents = $auth->money->getCents();
        $auth->card->setCardExpMonth(10);

        $response = $auth->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isFailed());
        $this->assertEqual($response->getMessage(), 'Authorization was declined');
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getStatus(), 'failed');
        $this->assertEqual($cents, $response->getResponse()->transaction->amount);

        $arResponse = $response->getResponseArray();
        $this->assertEqual($cents, $arResponse['transaction']['amount']);
    }

    public function test_errorAuthorization()
    {
        $auth = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $auth->money->setAmount($amount);
        $cents = $auth->money->getCents();
        $auth->card->setCardExpYear(10);

        $response = $auth->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertEqual($response->getMessage(), 'Exp year Invalid. Format should be: yyyy. Date is expired.');
        $this->assertEqual($response->getStatus(), 'error');
    }

    protected function getTestObject($threed = false)
    {
        $transaction = $this->getTestObjectInstance($threed);

        $transaction->money->setAmount(12.33);
        $transaction->money->setCurrency('EUR');
        $transaction->setDescription('test');
        $transaction->setTrackingId('my_custom_variable');
        $transaction->setLanguage('de');
        $transaction->setTestMode(true);

        $transaction->card->setCardNumber('4200000000000000');
        $transaction->card->setCardHolder('BEGATEWAY');
        $transaction->card->setCardExpMonth(1);
        $transaction->card->setCardExpYear(2030);
        $transaction->card->setCardCvc('123');

        $transaction->customer->setFirstName('John');
        $transaction->customer->setLastName('Doe');
        $transaction->customer->setCountry('LV');
        $transaction->customer->setAddress('Demo str 12');
        $transaction->customer->setCity('Riga');
        $transaction->customer->setZip('LV-1082');
        $transaction->customer->setIp('127.0.0.1');
        $transaction->customer->setEmail('john@example.com');
        $transaction->customer->setBirthDate('1970-01-01');

        return $transaction;
    }

    protected function getTestObjectInstance($threeds = false)
    {
        self::authorizeFromEnv($threeds);

        return new AuthorizationOperation();
    }
}
