<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\ApiClient;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class GatewayTransportExceptionTest extends TestCase
{
    private $gatewayBaseUrl;

    protected function setUp()
    {
        $this->gatewayBaseUrl = Settings::$gatewayBase;

        Settings::$gatewayBase = 'https://thedomaindoesntexist.begatewaynotexist.com';
    }

    protected function tearDown()
    {
        Settings::$gatewayBase = $this->gatewayBaseUrl;
    }

    public function testNetworkIssuesHandledCorrectly()
    {
        $request = $this->getTestRequest();

        $amount = mt_rand(0, 10000);

        $request->money = new Money($amount, 'EUR');

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isError());
        $this->assertContains('thedomaindoesntexist.begatewaynotexist.com', $response->getMessage());
    }

    private function getTestRequest()
    {
        $this->authorize();

        $money = new Money(1233, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');

        $request = new AuthorizationOperation($money, $customer);

        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setLanguage('de');
        $request->setTestMode(true);

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('BEGATEWAY');
        $request->card->setCardExpMonth(1);
        $request->card->setCardExpYear(2030);
        $request->card->setCardCvc('123');

        return $request;
    }
}
