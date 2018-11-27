<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\ApiClient;
use BeGateway\Contract\Request;
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
        $request = new QueryByTrackingId;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(QueryByTrackingId::class, $request);
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
        $request->setTrackingId('1234');

        $this->assertSame(Settings::$gatewayBase . '/v2/transactions/tracking_id/1234', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $this->assertSame(null, $request->data());
    }

    public function testQueryRequest()
    {
        $amount = mt_rand(0, 10000);

        $trackingId = bin2hex(openssl_random_pseudo_bytes(32));

        $parent = $this->runParentRequest($amount, $trackingId);

        $request = $this->getTestRequest();

        $request->setTrackingId($trackingId);

        $response = (new ApiClient)->send($request);

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
        $request = $this->getTestRequest();

        $request->setTrackingId('1234567890qwerty');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());

        $arTrx = $response->getResponse()->transactions;
        $this->assertSame([], $arTrx);
    }

    private function runParentRequest($amount, $trackingId = '12345')
    {
        $this->authorize();

        $money = new Money($amount, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');

        $request = new PaymentOperation($money, $customer);

        $request->setDescription('test');
        $request->setTrackingId($trackingId);
        $request->setTestMode(true);

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('John Doe');
        $request->card->setCardExpMonth(1);
        $request->card->setCardExpYear(2030);
        $request->card->setCardCvc('123');

        return (new ApiClient)->send($request);
    }

    private function getTestRequest()
    {
        $this->authorize();

        return new QueryByTrackingId;
    }
}
