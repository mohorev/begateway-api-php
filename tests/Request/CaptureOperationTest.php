<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Card;
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
        $money = new Money(1256, 'USD');
        $parentUid = '12345678';
        $request = $this->getTestRequest($money, $parentUid);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(CaptureOperation::class, $request);
    }

    public function testGetParentUid()
    {
        $money = new Money(1256, 'USD');
        $parentUid = '12345678';
        $request = $this->getTestRequest($money, $parentUid);

        $this->assertSame('12345678', $request->getParentUid());
    }

    public function testEndpoint()
    {
        $money = new Money(1256, 'USD');
        $parentUid = '12345678';
        $request = $this->getTestRequest($money, $parentUid);

        $this->assertSame(Settings::$gatewayBase . '/transactions/captures', $request->endpoint());
    }

    public function testData()
    {
        $money = new Money(1256, 'USD');
        $parentUid = '12345678';
        $request = $this->getTestRequest($money, $parentUid);

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

        $money = new Money($amount, 'USD');
        $parentUid = $parent->getUid();
        $request = $this->getTestRequest($money, $parentUid);

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

        $money = new Money($amount + 1, 'USD');
        $parentUid = $parent->getUid();
        $request = $this->getTestRequest($money, $parentUid);

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertContains("Amount can't be greater than", $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new Card('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'USD');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new AuthorizationOperation($card, $money, $customer, 'tracking_id');
        $request->setDescription('test');

        return $this->getApiClient()->send($request);
    }

    private function getTestRequest($money, $parentUid, $secure3D = false)
    {
        $this->authorize($secure3D);

        return new CaptureOperation($money, $parentUid);
    }
}
