<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\CreditCard;
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

        $request->setMoney(new Money($amount, 'EUR'));

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isError());
        $this->assertContains('thedomaindoesntexist.begatewaynotexist.com', $response->getMessage());
    }

    private function getTestRequest()
    {
        $this->authorize();

        $card = new CreditCard('4200000000000000', 'BEGATEWAY', 1, 2030, '123');

        $money = new Money(1233, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new AuthorizationOperation($card, $money, $customer);
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setLanguage('de');
        $request->setTestMode(true);

        return $request;
    }
}
