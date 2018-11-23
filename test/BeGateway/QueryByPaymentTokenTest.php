<?php

namespace BeGateway;

use BeGateway\Request\GetPaymentToken;
use BeGateway\Request\QueryByPaymentToken;

class QueryByPaymentTokenTest extends TestCase
{
    public function test_setToken()
    {
        $request = $this->getTestObjectInstance();

        $request->setToken('123456');
        $this->assertEqual($request->getToken(), '123456');
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();
        $request->setToken('1234');

        $this->assertEqual($request->endpoint(), Settings::$checkoutBase . '/ctp/api/checkouts/1234');
    }

    public function test_queryRequest()
    {
        $amount = rand(0, 10000);

        $parent = $this->runParentTransaction($amount);

        $request = $this->getTestObjectInstance();

        $request->setToken($parent->getToken());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertNotNull($response->getToken(), $parent->getToken());
    }

    public function test_queryResponseForUnknownUid()
    {
        $request = $this->getTestObjectInstance();

        $request->setToken('1234567890qwerty');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertEqual($response->getMessage(), 'Record not found');
    }

    protected function runParentTransaction($amount = 10.00)
    {
        self::authorizeFromEnv();

        $request = new GetPaymentToken();

        $url = 'http://www.example.com';

        $request->money->setAmount($amount);
        $request->money->setCurrency('EUR');
        $request->setAuthorizationTransactionType();
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setNotificationUrl($url . '/n');
        $request->setCancelUrl($url . '/c');
        $request->setSuccessUrl($url . '/s');
        $request->setDeclineUrl($url . '/d');
        $request->setFailUrl($url . '/f');

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

        return new QueryByPaymentToken();
    }
}
