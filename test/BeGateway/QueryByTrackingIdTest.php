<?php

namespace BeGateway;

use BeGateway\Request\PaymentOperation;
use BeGateway\Request\QueryByTrackingId;

class QueryByTrackingIdTest extends TestCase
{
    public function test_trackingId()
    {
        $request = $this->getTestObjectInstance();

        $request->setTrackingId('123456');
        $this->assertEqual($request->getTrackingId(), '123456');
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();
        $request->setTrackingId('1234');

        $this->assertEqual($request->endpoint(), Settings::$gatewayBase . '/v2/transactions/tracking_id/1234');
    }

    public function test_queryRequest()
    {
        $amount = rand(0, 10000);

        $trackingId = bin2hex(openssl_random_pseudo_bytes(32));

        $parent = $this->runParentTransaction($amount, $trackingId);

        $request = $this->getTestObjectInstance();

        $request->setTrackingId($trackingId);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());

        $arTrx = $response->getResponse()->transactions;

        $this->assertEqual(sizeof($arTrx), 1);
        $this->assertNotNull($arTrx[0]->uid);
        $this->assertEqual($arTrx[0]->amount, $amount * 100);
        $this->assertEqual($arTrx[0]->tracking_id, $trackingId);
        $this->assertEqual($parent->getUid(), $arTrx[0]->uid);
    }

    public function test_queryResponseForUnknownUid()
    {
        $request = $this->getTestObjectInstance();

        $request->setTrackingId('1234567890qwerty');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());

        $arTrx = $response->getResponse()->transactions;
        $this->assertEqual(sizeof($arTrx), 0);
    }

    protected function runParentTransaction($amount = 10.00, $trackingId = '12345')
    {
        self::authorizeFromEnv();

        $request = new PaymentOperation();

        $request->money->setAmount($amount);
        $request->money->setCurrency('EUR');
        $request->setDescription('test');
        $request->setTrackingId($trackingId);
        $request->setTestMode(true);

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

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new QueryByTrackingId();
    }
}
