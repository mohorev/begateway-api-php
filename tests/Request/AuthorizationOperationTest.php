<?php

namespace BeGateway\Tests\Request;

use BeGateway\ApiClient;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class AuthorizationOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = new AuthorizationOperation;

        $this->assertInstanceOf(AuthorizationOperation::class, $request);
    }

    public function testGetSetDescription()
    {
        $request = new AuthorizationOperation;

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertSame($description, $request->getDescription());
    }

    public function testGetSetTrackingId()
    {
        $request = new AuthorizationOperation;

        $trackingId = 'Test tracking_id';
        $request->setTrackingId($trackingId);
        $this->assertSame($trackingId, $request->getTrackingId());
    }

    public function testGetSetNotificationUrl()
    {
        $request = new AuthorizationOperation;

        $url = 'http://www.example.com';
        $request->setNotificationUrl($url);
        $this->assertSame($url, $request->getNotificationUrl());
    }

    public function testGetSetReturnUrl()
    {
        $request = new AuthorizationOperation;

        $url = 'http://www.example.com';
        $request->setReturnUrl($url);
        $this->assertSame($url, $request->getReturnUrl());
    }

    public function testGetSetTestMode()
    {
        $request = new AuthorizationOperation;

        $this->assertFalse($request->getTestMode());

        $request->setTestMode(true);
        $this->assertTrue($request->getTestMode());

        $request->setTestMode(false);
        $this->assertFalse($request->getTestMode());
    }

    public function testEndpoint()
    {
        $request = new AuthorizationOperation;

        $this->assertSame(Settings::$gatewayBase . '/transactions/authorizations', $request->endpoint());
    }

    public function testData()
    {
        $request = $this->getTestRequest();

        $expected = [
            'request' => [
                'amount' => 1233,
                'currency' => 'EUR',
                'description' => 'test',
                'tracking_id' => 'my_custom_variable',
                'notification_url' => null,
                'return_url' => null,
                'language' => 'de',
                'test' => true,
                'credit_card' => [
                    'number' => '4200000000000000',
                    'verification_value' => '123',
                    'holder' => 'BEGATEWAY',
                    'exp_month' => '01',
                    'exp_year' => '2030',
                    'token' => null,
                    'skip_three_d_secure_verification' => false,
                ],
                'customer' => [
                    'ip' => '127.0.0.1',
                    'email' => 'john@example.com',
                    'birth_date' => '1970-01-01',
                ],
                'billing_address' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'country' => 'LV',
                    'city' => 'Riga',
                    'state' => null,
                    'zip' => 'LV-1082',
                    'address' => 'Demo str 12',
                    'phone' => null,
                ],
                'additional_data' => [
                    'receipt_text' => [],
                    'contract' => [],
                ],
            ],
        ];

        $this->assertSame($expected, $request->data());

        $request->setTestMode(false);

        $expected['request']['test'] = false;
        $this->assertSame($expected, $request->data());
    }

    public function testSuccessAuthorization()
    {
        $request = $this->getTestRequest();

        $amount = mt_rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $cents = $request->money->getCents();

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('successful', $response->getStatus());
        $this->assertSame($cents, $response->getResponse()->transaction->amount);
        $this->assertSame($cents, $response->getResponseArray()['transaction']['amount']);
    }

    public function testIncompleteAuthorization()
    {
        $request = $this->getTestRequest(true);

        $amount = mt_rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $request->card->setCardNumber('4012001037141112');
        $cents = $request->money->getCents();

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isIncomplete());
        $this->assertNull($response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertNotNull($response->getResponse()->transaction->redirect_url);
        $this->assertContains('process', $response->getResponse()->transaction->redirect_url);
        $this->assertSame('incomplete', $response->getStatus());
        $this->assertSame($cents, $response->getResponse()->transaction->amount);
        $this->assertSame($cents, $response->getResponseArray()['transaction']['amount']);
    }

    public function testFailedAuthorization()
    {
        $request = $this->getTestRequest();
        $request->card->setCardNumber('4005550000000019');

        $amount = mt_rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $cents = $request->money->getCents();
        $request->card->setCardExpMonth(10);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isFailed());
        $this->assertSame('Authorization was declined', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('failed', $response->getStatus());
        $this->assertSame($cents, $response->getResponse()->transaction->amount);
        $this->assertSame($cents, $response->getResponseArray()['transaction']['amount']);
    }

    public function testErrorAuthorization()
    {
        $request = $this->getTestRequest();

        $amount = mt_rand(0, 10000) / 100;

        $request->money->setAmount($amount);
        $request->card->setCardExpYear(10);

        $response = (new ApiClient)->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('Exp year Invalid. Format should be: yyyy. Date is expired.', $response->getMessage());
        $this->assertSame('error', $response->getStatus());
    }

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $request = new AuthorizationOperation;

        $request->money->setAmount(12.33);
        $request->money->setCurrency('EUR');
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setLanguage('de');
        $request->setTestMode(true);

        $request->card->setCardNumber('4200000000000000');
        $request->card->setCardHolder('BEGATEWAY');
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
        $request->customer->setBirthDate('1970-01-01');

        return $request;
    }
}
