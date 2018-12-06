<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;
use BeGateway\Request\QueryByUid;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class QueryByUidTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest('123456');

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(QueryByUid::class, $request);
    }

    public function testGetUid()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame('123456', $request->getUid());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame(Settings::$gatewayBase . '/transactions/123456', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame(null, $request->data());
    }

    public function testQueryRequest()
    {
        $this->authorize();

        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest($parent->getUid());

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame($parent->getUid(), $response->getUid());
    }

    public function testQueryResponseForUnknownUid()
    {
        $request = $this->getTestRequest('1234567890qwerty');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertSame('Record not found', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $card = new Card('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $transaction = new PaymentOperation($card, $money, $customer, 'tracking_id');
        $transaction->setDescription('test');

        return $this->getApiClient()->send($transaction);
    }

    private function getTestRequest($uid)
    {
        $this->authorize();

        return new QueryByUid($uid);
    }
}
