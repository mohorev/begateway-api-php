<?php

namespace BeGateway\Tests\Request;

use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CardToken;
use BeGateway\Request\CardTokenUpdate;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;
use BeGateway\Token;

class CardTokenTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(CardToken::class, $request);
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

        $this->assertSame(Settings::$gatewayBase . '/credit_cards', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
            'request' => [
                'number' => '4200000000000000',
                'holder' => 'John Smith',
                'exp_month' => '02',
                'exp_year' => '2030',
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testSuccessTokenCreationUpdateAndAuthorization()
    {
        $request = $this->getTestRequest();

        # create token
        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('John Smith', $response->holder);
        $this->assertSame('visa', $response->brand);
        $this->assertSame('4', $response->first1);
        $this->assertSame('0000', $response->last4);
        $this->assertSame('02', $response->expMonth);
        $this->assertSame('2030', $response->expYear);
        $this->assertNotNull($response->token);

        $token = $response->token;

        # update token
        $request = new CardTokenUpdate($token, 'John Doe', 1, 2050);

        $response2 = $this->getApiClient()->send($request);

        $this->assertSame('John Doe', $response2->holder);
        $this->assertSame('visa', $response2->brand);
        $this->assertSame('4', $response2->first1);
        $this->assertSame('0000', $response2->last4);
        $this->assertSame('01', $response2->expMonth);
        $this->assertSame('2050', $response2->expYear);
        $this->assertNotNull($response2->token);
        $this->assertSame($response2->token, $token);

        # make authorization with token
        $request = $this->getAuthorizationRequest($token);

        $amount = $request->getMoney()->getAmount();

        $response3 = $this->getApiClient()->send($request);
        $this->assertTrue($response3->isValid());
        $this->assertTrue($response3->isSuccess());
        $this->assertSame('Successfully processed', $response3->getMessage());
        $this->assertNotNull($response3->getUid());
        $this->assertSame('successful', $response3->getStatus());
        $this->assertSame($amount, $response3->getResponse()->transaction->amount);
    }

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        return new CardToken('4200000000000000', 'John Smith', 2, 2030);
    }

    public function getAuthorizationRequest($cardToken)
    {
        $this->authorize();

        $token = new Token($cardToken, false);

        $money = new Money(mt_rand(0, 10000), 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);

        $request = new AuthorizationOperation($token, $money, $customer, 'tracking_id');
        $request->setDescription('test');

        return $request;
    }
}
