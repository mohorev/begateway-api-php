<?php

namespace BeGateway;

use BeGateway\Request\PaymentOperation;
use BeGateway\Request\RefundOperation;

class RefundOperationTest extends TestCase
{
    public function test_setParentUid()
    {
        $request = $this->getTestObjectInstance();

        $uid = '1234567';
        $request->setParentUid($uid);
        $this->assertEqual($uid, $request->getParentUid());
    }

    public function test_setReason()
    {
        $request = $this->getTestObjectInstance();

        $reason = 'test reason';
        $request->setReason($reason);
        $this->assertEqual($reason, $request->getReason());
    }

    public function test_buildRequestMessage()
    {
        $request = $this->getTestObject();
        $arr = [
            'request' => [
                'parent_uid' => '12345678',
                'amount' => 1256,
                'reason' => 'merchant request',
            ],
        ];

        $this->assertEqual($arr, $request->data());
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();

        $this->assertEqual($request->endpoint(), Settings::$gatewayBase . '/transactions/refunds');
    }

    public function test_successRefundRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $request = $this->getTestObjectInstance();

        $request->money->setAmount($amount);
        $request->setParentUid($parent->getUid());
        $request->setReason('test reason');

        $response = (new ApiClient)->send($request);

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

        $request = $this->getTestObjectInstance();

        $request->money->setAmount($amount + 1);
        $request->setParentUid($parent->getUid());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertTrue(preg_match('/Reason can\'t be blank./', $response->getMessage()));
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

        $request->setParentUid('12345678');
        $request->money->setAmount(12.56);
        $request->setReason('merchant request');

        return $request;
    }

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new RefundOperation();
    }
}
