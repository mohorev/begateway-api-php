<?php

namespace BeGateway;

use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;

class CreditOperationTest extends TestCase
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

    public function test_buildRequestMessage()
    {
        $request = $this->getTestObject();

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

        $this->assertEqual($arr, $request->data());
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();

        $this->assertEqual($request->endpoint(), Settings::$gatewayBase . '/transactions/credits');
    }

    public function test_successCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $request = $this->getTestObjectInstance();

        $request->money->setAmount($amount * 2);
        $request->money->setCurrency('EUR');
        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');
        $request->card->setCardToken($parent->getResponse()->transaction->credit_card->token);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getMessage(), 'Successfully processed');
    }

    public function test_errorCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $request = $this->getTestObjectInstance();

        $request->money->setAmount($amount * 2);
        $request->money->setCurrency('EUR');
        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');
        $request->card->setCardToken('12345');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertPattern('|Token does not exist.|', $response->getMessage());
    }

    protected function runParentTransaction($amount = 10.00)
    {
        self::authorizeFromEnv();

        $request = new PaymentOperation();

        $request->money->setAmount($amount);
        $request->money->setCurrency('EUR');
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('John Doe');
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

        return (new ApiClient)->send($request);
    }

    protected function getTestObject()
    {
        $request = $this->getTestObjectInstance();

        $request->money->setAmount(12.56);
        $request->money->setCurrency('RUB');
        $request->card->setCardToken('12345');
        $request->setDescription('description');
        $request->setTrackingId('tracking');

        return $request;
    }

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new CreditOperation();
    }
}
