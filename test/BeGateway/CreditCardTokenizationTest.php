<?php

namespace BeGateway;

use BeGateway\Request\AuthorizationOperation;
use BeGateway\Request\CardToken;

class CreditCardTokenizationTest extends TestCase
{
    public function test_buildRequestMessage()
    {
        $request = $this->getTestObject();
        $arr = [
            'request' => [
                'number' => '4200000000000000',
                'holder' => 'John Smith',
                'exp_month' => '02',
                'exp_year' => '2030',
                'token' => '',
            ],
        ];

        $this->assertEqual($arr, $request->data());
    }

    public function test_endpoint()
    {
        $request = $this->getTestObjectInstance();

        $this->assertEqual($request->endpoint(), Settings::$gatewayBase . '/credit_cards');
    }

    public function test_successTokenCreationUpdateAndAuthorization()
    {
        $request = $this->getTestObject();

        # create token
        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertEqual($response->card->getCardHolder(), 'John Smith');
        $this->assertEqual($response->card->getBrand(), 'visa');
        $this->assertEqual($response->card->getFirst1(), '4');
        $this->assertEqual($response->card->getLast4(), '0000');
        $this->assertEqual($response->card->getCardExpMonth(), '2');
        $this->assertEqual($response->card->getCardExpYear(), '2030');
        $this->assertNotNull($response->card->getCardToken());

        # update token
        $request->card->setCardExpMonth(1);
        $request->card->setCardHolder('John Doe');
        $oldToken = $response->card->getCardToken();
        $request->card->setCardToken($oldToken);
        $request->card->setCardNumber(null);

        $response2 = (new ApiClient)->send($request);
        $this->assertEqual($response2->card->getCardHolder(), 'John Doe');
        $this->assertEqual($response2->card->getBrand(), 'visa');
        $this->assertEqual($response2->card->getFirst1(), '4');
        $this->assertEqual($response2->card->getLast4(), '0000');
        $this->assertEqual($response2->card->getCardExpMonth(), '1');
        $this->assertEqual($response2->card->getCardExpYear(), '2030');
        $this->assertNotNull($response2->card->getCardToken());
        $this->assertEqual($response2->card->getCardToken(), $oldToken);

        # make authorization with token
        $amount = rand(0, 10000) / 100;

        $request = $this->getAuthorizationTestObject();

        $request->money->setAmount($amount);
        $cents = $request->money->getCents();

        $request->card->setCardToken($response2->card->getCardToken());
        $request->card->setCardCvc('123');

        $response3 = (new ApiClient)->send($request);
        $this->assertTrue($response3->isValid());
        $this->assertTrue($response3->isSuccess());
        $this->assertEqual($response3->getMessage(), 'Successfully processed');
        $this->assertNotNull($response3->getUid());
        $this->assertEqual($response3->getStatus(), 'successful');
        $this->assertEqual($cents, $response3->getResponse()->transaction->amount);
    }

    protected function getTestObject($threed = false)
    {
        $request = $this->getTestObjectInstance($threed);

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('John Smith');
        $request->card->setCardExpMonth(2);
        $request->card->setCardExpYear(2030);

        return $request;
    }

    protected function getAuthorizationTestObject($threed = false)
    {
        $request = $this->getAuthorizationTestObjectInstance($threed);

        $request->money->setCurrency('EUR');
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

    protected function getTestObjectInstance($threed = false)
    {
        self::authorizeFromEnv($threed);

        return new CardToken();
    }

    protected function getAuthorizationTestObjectInstance($threed = false)
    {
        self::authorizeFromEnv($threed);

        return new AuthorizationOperation();
    }
}
