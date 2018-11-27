<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\VoidOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class VoidOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = new VoidOperation;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(VoidOperation::class, $request);
    }

    public function testGetSetParentUid()
    {
        $request = $this->getTestRequest();

        $uid = '1234567';
        $request->setParentUid($uid);
        $this->assertSame($uid, $request->getParentUid());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$gatewayBase . '/transactions/voids', $request->endpoint());
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

    public function testSuccessVoidRequest()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount, 'EUR');
        $request->setParentUid($parent->getUid());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertSame($parent->getUid(), $response->getResponse()->transaction->parent_uid);
    }

    public function testErrorVoidRequest()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount + 1, 'EUR');
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

        $request->money = new Money($amount, 'EUR');

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

        $request = new VoidOperation;

        $request->setParentUid('12345678');
        $request->money = new Money(1256, 'EUR');

        return $request;
    }
}
