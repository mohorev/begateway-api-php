<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Request\PaymentOperation;
use BeGateway\Request\RefundOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class RefundOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = new RefundOperation;

        $this->assertInstanceOf(RefundOperation::class, $request);
    }

    public function testGetSetParentUid()
    {
        $request = $this->getTestRequest();

        $uid = '1234567';
        $request->setParentUid($uid);
        $this->assertSame($uid, $request->getParentUid());
    }

    public function testGetSetReason()
    {
        $request = $this->getTestRequest();

        $reason = 'test reason';
        $request->setReason($reason);
        $this->assertSame($reason, $request->getReason());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$gatewayBase . '/transactions/refunds', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
            'request' => [
                'parent_uid' => '12345678',
                'amount' => 1256,
                'reason' => 'merchant request',
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testSuccessRefundRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money->setAmount($amount);
        $request->setParentUid($parent->getUid());
        $request->setReason('test reason');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertSame($parent->getUid(), $response->getResponse()->transaction->parent_uid);
    }

    public function testErrorRefundRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money->setAmount($amount + 1);
        $request->setParentUid($parent->getUid());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertContains("Amount can't be greater than", $response->getMessage());
    }

    public function testErrorRefundRequestWitoutReason()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money->setAmount($amount);
        $request->setParentUid($parent->getUid());
        $request->setReason('');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame("Reason can't be blank.", $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $request = new PaymentOperation;

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

    private function getTestRequest()
    {
        $this->authorize();

        $request = new RefundOperation;

        $request->setParentUid('12345678');
        $request->money->setAmount(12.56);
        $request->setReason('merchant request');

        return $request;
    }
}
