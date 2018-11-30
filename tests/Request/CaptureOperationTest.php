<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CaptureOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class CaptureOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(CaptureOperation::class, $request);
    }

    public function testGetSetParentUid()
    {
        $request = $this->getTestRequest();

        $uid = '10314520-438c04b473';
        $request->setParentUid($uid);
        $this->assertSame($uid, $request->getParentUid());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$gatewayBase . '/transactions/captures', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
            'request' => [
                'parent_uid' => '12345678',
                'amount' => 1256,
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testSuccessCapture()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount, 'USD');
        $request->setParentUid($parent->getUid());

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertSame($response->getResponse()->transaction->parent_uid, $parent->getUid());
    }

    public function testErrorCapture()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount + 1, 'USD');
        $request->setParentUid($parent->getUid());

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertContains("Amount can't be greater than", $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new CreditCard('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'USD');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');

        $request = new AuthorizationOperation($card, $money, $customer);
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');

        return $this->getApiClient()->send($request);
    }

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $money = new Money(1256, 'USD');
        $request = new CaptureOperation($money);

        $request->setParentUid('12345678');

        return $request;
    }
}
