<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;
use BeGateway\Request\QueryByTrackingId;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class QueryByTrackingIdTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest('123456');

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(QueryByTrackingId::class, $request);
    }

    public function testGetTrackingId()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame('123456', $request->getTrackingId());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame(Settings::$gatewayBase . '/v2/transactions/tracking_id/123456', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest('123456');

        $this->assertSame(null, $request->data());
    }

    public function testQueryRequest()
    {
        $amount = mt_rand(0, 10000);

        $trackingId = bin2hex(openssl_random_pseudo_bytes(32));

        $parent = $this->runParentRequest($amount, $trackingId);

        $request = $this->getTestRequest($trackingId);

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());

        $arTrx = $response->getResponse()->transactions;

        $this->assertCount(1, $arTrx);
        $this->assertNotNull($arTrx[0]->uid);
        $this->assertSame($amount, $arTrx[0]->amount);
        $this->assertSame($trackingId, $arTrx[0]->tracking_id);
        $this->assertSame($parent->getUid(), $arTrx[0]->uid);
    }

    public function testQueryResponseForUnknownUid()
    {
        $request = $this->getTestRequest('1234567890qwerty');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());

        $arTrx = $response->getResponse()->transactions;
        $this->assertSame([], $arTrx);
    }

    private function runParentRequest($amount, $trackingId = '12345')
    {
        $this->authorize();

        $card = new Card('4200000000000000', 'John Doe', 1, 2030, '123');

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new PaymentOperation($card, $money, $customer, $trackingId);
        $request->setDescription('test');
        $request->setTestMode(true);

        return $this->getApiClient()->send($request);
    }

    private function getTestRequest($trackingId)
    {
        $this->authorize();

        return new QueryByTrackingId($trackingId);
    }
}
