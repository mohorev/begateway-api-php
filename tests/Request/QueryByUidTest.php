<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\ApiClient;
use BeGateway\Contract\Request;
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
        $request = new QueryByUid;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(QueryByUid::class, $request);
    }

    public function testGetSetUid()
    {
        $request = $this->getTestRequest();

        $uid = '123456';
        $request->setUid($uid);
        $this->assertSame($uid, $request->getUid());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();
        $request->setUid('1234');

        $this->assertSame(Settings::$gatewayBase . '/transactions/1234', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $this->assertSame(null, $request->data());
    }

    public function testQueryRequest()
    {
        $amount = mt_rand(0, 10000);

        $parent = $this->runParentRequest($amount);

        $request = $this->getTestRequest();
        $request->setUid($parent->getUid());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getUid());
        $this->assertSame($parent->getUid(), $response->getUid());
    }

    public function testQueryResponseForUnknownUid()
    {
        $request = $this->getTestRequest();
        $request->setUid('1234567890qwerty');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertSame('Record not found', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');

        $transaction = new PaymentOperation($money, $customer);

        $transaction->setDescription('test');
        $transaction->setTrackingId('my_custom_variable');

        $transaction->card->setCardNumber('4200000000000000');
        $transaction->card->setCardHolder('John Doe');
        $transaction->card->setCardExpMonth(1);
        $transaction->card->setCardExpYear(2030);
        $transaction->card->setCardCvc('123');

        return (new ApiClient)->send($transaction);
    }

    private function getTestRequest()
    {
        $this->authorize();

        return new QueryByUid;
    }
}
