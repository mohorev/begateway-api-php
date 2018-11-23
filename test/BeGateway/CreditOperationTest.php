<?php

namespace BeGateway;

class CreditOperationTest extends TestCase
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

    public function test_buildRequestMessage()
    {
        $transaction = $this->getTestObject();
        $arr = [
            'request' => [
                'amount' => 1256,
                'currency' => 'RUB',
                'description' => 'description',
                'tracking_id' => 'tracking',
                'credit_card' => [
                    'token' => '12345',
                ],
            ],
        ];

        $reflection = new \ReflectionClass('BeGateway\CreditOperation');
        $method = $reflection->getMethod('buildRequestMessage');
        $method->setAccessible(true);

        $request = $method->invoke($transaction, 'buildRequestMessage');

        $this->assertEqual($arr, $request);
    }

    public function test_endpoint()
    {
        $auth = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\CreditOperation');
        $method = $reflection->getMethod('endpoint');
        $method->setAccessible(true);
        $url = $method->invoke($auth, 'endpoint');

        $this->assertEqual($url, Settings::$gatewayBase . '/transactions/credits');

    }

    public function test_successCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $transaction = $this->getTestObjectInstance();

        $transaction->money->setAmount($amount * 2);
        $transaction->money->setCurrency('EUR');
        $transaction->setDescription('test description');
        $transaction->setTrackingId('tracking_id');
        $transaction->card->setCardToken($parent->getResponse()->transaction->credit_card->token);

        $response = $transaction->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getMessage(), 'Successfully processed');

    }

    public function test_errorCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $transaction = $this->getTestObjectInstance();

        $transaction->money->setAmount($amount * 2);
        $transaction->money->setCurrency('EUR');
        $transaction->setDescription('test description');
        $transaction->setTrackingId('tracking_id');
        $transaction->card->setCardToken('12345');

        $response = $transaction->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertPattern('|Token does not exist.|', $response->getMessage());
    }

    protected function runParentTransaction($amount = 10.00)
    {
        self::authorizeFromEnv();

        $transaction = new PaymentOperation();

        $transaction->money->setAmount($amount);
        $transaction->money->setCurrency('EUR');
        $transaction->setDescription('test');
        $transaction->setTrackingId('my_custom_variable');

        $transaction->card->setCardNumber('4200000000000000');
        $transaction->card->setCardHolder('John Doe');
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

        return $transaction->submit();
    }

    protected function getTestObject()
    {
        $transaction = $this->getTestObjectInstance();

        $transaction->money->setAmount(12.56);
        $transaction->money->setCurrency('RUB');
        $transaction->card->setCardToken('12345');
        $transaction->setDescription('description');
        $transaction->setTrackingId('tracking');

        return $transaction;

    }

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new CreditOperation();
    }
}
