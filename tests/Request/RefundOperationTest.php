<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;
use BeGateway\Request\RefundOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class RefundOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest();

        $this->assertInstanceOf(Request::class, $request);
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
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount, 'EUR');

        $request->setParentUid($parent->getUid());
        $request->setReason('test reason');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertSame($parent->getUid(), $response->getResponse()->transaction->parent_uid);
    }

    public function testErrorRefundRequest()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount + 1, 'EUR');
        $request->setParentUid($parent->getUid());

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertContains("Amount can't be greater than", $response->getMessage());
    }

    public function testErrorRefundRequestWitoutReason()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount, 'EUR');
        $request->setParentUid($parent->getUid());
        $request->setReason('');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame("Reason can't be blank.", $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new CreditCard('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new PaymentOperation($card, $money, $customer);

        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');

        return $this->getApiClient()->send($request);
    }

    private function getTestRequest()
    {
        $money = new Money(1256, 'EUR');

        $request = new RefundOperation($money);

        $request->setParentUid('12345678');
        $request->setReason('merchant request');

        return $request;
    }
}
