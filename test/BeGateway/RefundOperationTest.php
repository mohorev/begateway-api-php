<?php

namespace BeGateway;

use BeGateway\Request\PaymentOperation;
use BeGateway\Request\RefundOperation;

class RefundOperationTest extends TestCase
{
    public function test_setParentUid()
    {
        $transaction = $this->getTestObjectInstance();
        $uid = '1234567';

        $transaction->setParentUid($uid);

        $this->assertEqual($uid, $transaction->getParentUid());
    }

    public function test_setReason()
    {
        $reason = 'test reason';
        $transaction = $this->getTestObjectInstance();
        $transaction->setReason($reason);
        $this->assertEqual($reason, $transaction->getReason());
    }

    public function test_buildRequestMessage()
    {
        $transaction = $this->getTestObject();
        $arr = [
            'request' => [
                'parent_uid' => '12345678',
                'amount' => 1256,
                'reason' => 'merchant request',
            ],
        ];

        $reflection = new \ReflectionClass('BeGateway\Request\RefundOperation');
        $method = $reflection->getMethod('buildRequestMessage');
        $method->setAccessible(true);

        $request = $method->invoke($transaction, 'buildRequestMessage');

        $this->assertEqual($arr, $request);
    }

    public function test_endpoint()
    {
        $auth = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\Request\RefundOperation');
        $method = $reflection->getMethod('endpoint');
        $method->setAccessible(true);
        $url = $method->invoke($auth, 'endpoint');

        $this->assertEqual($url, Settings::$gatewayBase . '/transactions/refunds');

    }

    public function test_successRefundRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $transaction = $this->getTestObjectInstance();

        $transaction->money->setAmount($amount);
        $transaction->setParentUid($parent->getUid());
        $transaction->setReason('test reason');

        $response = $transaction->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertEqual($response->getMessage(), 'Successfully processed');
        $this->assertEqual($response->getResponse()->transaction->parent_uid, $parent->getUid());
    }

    public function test_errorRefundRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $transaction = $this->getTestObjectInstance();

        $transaction->money->setAmount($amount + 1);
        $transaction->setParentUid($parent->getUid());

        $response = $transaction->submit();

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertTrue(preg_match('/Reason can\'t be blank./', $response->getMessage()));
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

        $transaction->setParentUid('12345678');

        $transaction->money->setAmount(12.56);
        $transaction->setReason('merchant request');

        return $transaction;
    }

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new RefundOperation();
    }
}
