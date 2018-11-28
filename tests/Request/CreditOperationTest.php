<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;
use BeGateway\TokenCard;

class CreditOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest();

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
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount * 2, 'EUR');

        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');
        $request->card = new TokenCard($parent->getResponse()->transaction->credit_card->token);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame('Successfully processed', $response->getMessage());
    }

    public function testErrorCreditRequest()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();

        $request->money = new Money($amount * 2, 'EUR');

        $request->setDescription('test description');
        $request->setTrackingId('tracking_id');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('Token does not exist.', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new CreditCard('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');

        $request = new PaymentOperation($card, $money, $customer);
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');

        return (new ApiClient)->send($request);
    }

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $card = new TokenCard('12345');

        $money = new Money(1256, 'RUB');

        $request = new CreditOperation($card, $money);
        $request->setDescription('description');
        $request->setTrackingId('tracking');

        return $request;
    }
}
