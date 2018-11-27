<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CaptureOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class CaptureOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = new CaptureOperation;

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

        $response = (new ApiClient)->send($request);

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

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertContains("Amount can't be greater than", $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $request = new AuthorizationOperation;

        $request->money = new Money($amount, 'USD');

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

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $request = new CaptureOperation;

        $request->setParentUid('12345678');
        $request->money = new Money(1256, 'USD');

        return $request;
    }
}
