<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CardToken;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class CardTokenTest extends TestCase
{
    public function testCreate()
    {
        $request = new CardToken;

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
                'holder' => 'John Smith',
                'number' => '4200000000000000',
                'exp_month' => '02',
                'exp_year' => '2030',
                'token' => null,
            ],
        ];

        $this->assertSame($expected, $request->data());
    }

    public function testSuccessTokenCreationUpdateAndAuthorization()
    {
        $request = $this->getTestRequest();

        # create token
        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('John Smith', $response->card->getCardHolder());
        $this->assertSame('visa', $response->card->getBrand());
        $this->assertSame('4', $response->card->getFirst1());
        $this->assertSame('0000', $response->card->getLast4());
        $this->assertSame('02', $response->card->getCardExpMonth());
        $this->assertSame('2030', $response->card->getCardExpYear());
        $this->assertNotNull($response->card->getCardToken());

        # update token
        $request->card->setCardExpMonth(1);
        $request->card->setCardHolder('John Doe');
        $oldToken = $response->card->getCardToken();
        $request->card->setCardToken($oldToken);
        $request->card->setCardNumber(null);

        $response2 = (new ApiClient)->send($request);
        $this->assertSame('John Doe', $response2->card->getCardHolder());
        $this->assertSame('visa', $response2->card->getBrand());
        $this->assertSame('4', $response2->card->getFirst1());
        $this->assertSame('0000', $response2->card->getLast4());
        $this->assertSame('01', $response2->card->getCardExpMonth());
        $this->assertSame('2030', $response2->card->getCardExpYear());
        $this->assertSame($response2->card->getCardToken(), $oldToken);
        $this->assertNotNull($response2->card->getCardToken());

        # make authorization with token
        $request = $this->getAuthorizationRequest();

        $amount = $request->money->getAmount();

        $request->card->setCardToken($response2->card->getCardToken());
        $request->card->setCardCvc('123');

        $response3 = (new ApiClient)->send($request);
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

        $request = new CardToken;

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('John Smith');
        $request->card->setCardExpMonth(2);
        $request->card->setCardExpYear(2030);

        return $request;
    }

    public function getAuthorizationRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $request = new AuthorizationOperation;

        $request->money = new Money(mt_rand(0, 10000), 'EUR');

        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');

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
}
