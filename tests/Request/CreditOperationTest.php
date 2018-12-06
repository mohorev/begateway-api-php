<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\CreditOperation;
use BeGateway\Request\PaymentOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;
use BeGateway\Token;

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

    public function testGetTrackingId()
    {
        $request = $this->getTestRequest();

        $this->assertSame('tracking_id', $request->getTrackingId());
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
                'tracking_id' => 'tracking_id',
                'description' => 'description',
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

        $money = new Money($amount * 2, 'EUR');
        $token = new Token($parent->getResponse()->transaction->credit_card->token);

        $request = new CreditOperation($money, $token, 'tracking_id');
        $request->setDescription('test description');

        $response = $this->getApiClient()->send($request);

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

        $money = new Money($amount * 2, 'EUR');
        $token = new Token('invalid-token');

        $request = new CreditOperation($money, $token, 'tracking_id');
        $request->setDescription('test description');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('Token does not exist.', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new Card('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new PaymentOperation($card, $money, $customer, 'tracking_id');
        $request->setDescription('test');

        return $this->getApiClient()->send($request);
    }

    private function getTestRequest()
    {
        $money = new Money(1256, 'RUB');
        $token = new Token('12345');

        $request = new CreditOperation($money, $token, 'tracking_id');
        $request->setDescription('description');

        return $request;
    }
}
