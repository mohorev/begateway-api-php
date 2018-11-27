<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Request\GetPaymentToken;
use BeGateway\Request\QueryByPaymentToken;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class QueryByPaymentTokenTest extends TestCase
{
    public function testCreate()
    {
        $request = new QueryByPaymentToken;

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(QueryByPaymentToken::class, $request);
    }

    public function testGetSetToken()
    {
        $request = $this->getTestRequest();

        $token = '123456';
        $request->setToken($token);
        $this->assertSame($request->getToken(), $token);
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();
        $request->setToken('1234');

        $this->assertSame(Settings::$checkoutBase . '/ctp/api/checkouts/1234', $request->endpoint());
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

        $request->setToken($parent->getToken());

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertNotNull($response->getToken(), $parent->getToken());
    }

    public function testQueryResponseForUnknownUid()
    {
        $request = $this->getTestRequest();

        $request->setToken('1234567890qwerty');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertSame('Record not found', $response->getMessage());
    }

    private function runParentRequest($amount)
    {
        $this->authorize();

        $request = new GetPaymentToken;

        $url = 'http://www.example.com';

        $request->money = new Money($amount, 'EUR');

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

    private function getTestRequest()
    {
        $this->authorize();

        return new QueryByPaymentToken;
    }
}
