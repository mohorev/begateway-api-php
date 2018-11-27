<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class CreditOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = new CreditOperation;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(CreditOperation::class, $request);
    }

    public function testGetSetDescription()
    {
        $request = $this->getTestRequest();

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertSame($description, $request->getDescription());
    }

    public function testGetSetTrackingId()
    {
        $request = $this->getTestRequest();

        $trackingId = 'test_tracking_id';
        $request->setTrackingId($trackingId);
        $this->assertSame($trackingId, $request->getTrackingId());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$gatewayBase . '/transactions/credits', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
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

        $this->assertSame($expected, $request->data());
    }

    public function testSuccessCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money->setAmount($amount * 2);
        $request->money->setCurrency('EUR');
        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');
        $request->card->setCardToken($parent->getResponse()->transaction->credit_card->token);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
    }

    public function testErrorCreditRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money->setAmount($amount * 2);
        $request->money->setCurrency('EUR');
        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');
        $request->card->setCardToken('12345');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('Token does not exist.', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

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

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $request = new CreditOperation;

        $request->money->setAmount(12.56);
        $request->money->setCurrency('RUB');
        $request->card->setCardToken('12345');
        $request->setDescription('description');
        $request->setTrackingId('tracking');

        return $request;
    }
}
