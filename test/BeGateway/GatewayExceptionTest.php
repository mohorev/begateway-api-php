<?php

namespace BeGateway;

use BeGateway\Request\AuthorizationOperation;

class GatewayTransportExceptionTest extends TestCase
{
    private $_apiBase;

    function setUp()
    {
        $this->_apiBase = Settings::$gatewayBase;

        Settings::$gatewayBase = 'https://thedomaindoesntexist.begatewaynotexist.com';
    }

    function tearDown()
    {
        Settings::$gatewayBase = $this->_apiBase;
    }

    public function test_networkIssuesHandledCorrectly()
    {
        $request = $this->getTestObject();

        $amount = rand(0, 10000) / 100;

        $request->money->setAmount($amount);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isError());
        $this->assertPattern("|thedomaindoesntexist.begatewaynotexist.com|", $response->getMessage());
    }

    protected function getTestObject($threed = false)
    {
        $request = $this->getTestObjectInstance($threed);

        $request->money->setAmount(12.33);
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

        return $request;
    }

    protected function getTestObjectInstance($threed = false)
    {
        self::authorizeFromEnv($threed);

        return new AuthorizationOperation();
    }
}
