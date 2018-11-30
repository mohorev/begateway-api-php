<?php

namespace BeGateway\Tests\Request;

use BeGateway\AdditionalData;
use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\AuthorizationOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class AuthorizationOperationTest extends TestCase
{
    public function testCreate()
    {
        $request = $this->getTestRequest();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(AuthorizationOperation::class, $request);
    }

    public function testGetSetDescription()
    {
        $request = $this->getTestRequest();

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertSame($description, $request->getDescription());
    }

    public function testGetSetTrackingId()
    {
        $request = $this->getTestRequest();

        $trackingId = 'test_tracking_id';
        $request->setTrackingId($trackingId);
        $this->assertSame($trackingId, $request->getTrackingId());
    }

    public function testGetSetNotificationUrl()
    {
        $request = $this->getTestRequest();

        $url = 'http://www.example.com';
        $request->setNotificationUrl($url);
        $this->assertSame($url, $request->getNotificationUrl());
    }

    public function testGetSetReturnUrl()
    {
        $request = $this->getTestRequest();

        $url = 'http://www.example.com';
        $request->setReturnUrl($url);
        $this->assertSame($url, $request->getReturnUrl());
    }

    public function testGetSetTestMode()
    {
        $request = $this->getTestRequest();

        $this->assertTrue($request->getTestMode());

        $request->setTestMode(false);
        $this->assertFalse($request->getTestMode());

        $request->setTestMode(true);
        $this->assertTrue($request->getTestMode());
    }

    public function testEndpoint()
    {
        $request = $this->getTestRequest();

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
                'customer' => [
                    'ip' => '127.0.0.1',
                    'email' => 'john@example.com',
                    'birth_date' => '1970-01-01',
                ],
                'credit_card' => [
                    'number' => '4200000000000000',
                    'verification_value' => '123',
                    'holder' => 'BEGATEWAY',
                    'exp_month' => '01',
                    'exp_year' => '2030',
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

        $request->money = new Money(mt_rand(0, 10000), 'EUR');
        $amount = $request->money->getAmount();

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('successful', $response->getStatus());
        $this->assertSame($amount, $response->getResponse()->transaction->amount);
    }

    public function testIncompleteAuthorization()
    {
        $request = $this->getTestRequest(true);

        $request->card = $this->getInvalidCard();
        $request->money = new Money(mt_rand(0, 10000), 'EUR');
        $amount = $request->money->getAmount();

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isIncomplete());
        $this->assertNull($response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertNotNull($response->getResponse()->transaction->redirect_url);
        $this->assertContains('process', $response->getResponse()->transaction->redirect_url);
        $this->assertSame('incomplete', $response->getStatus());
        $this->assertSame($amount, $response->getResponse()->transaction->amount);
    }

    public function testFailedAuthorization()
    {
        $request = $this->getTestRequest();

        $request->card = $this->getUnauthorizedCard();
        $request->money = new Money(mt_rand(0, 10000), 'EUR');
        $amount = $request->money->getAmount();

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isFailed());
        $this->assertSame('Authorization was declined', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('failed', $response->getStatus());
        $this->assertSame($amount, $response->getResponse()->transaction->amount);
    }

    public function testErrorAuthorization()
    {
        $request = $this->getTestRequest();

        $request->card = $this->getExpInvalidCard();
        $request->money = new Money(mt_rand(0, 10000), 'EUR');

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isError());
        $this->assertSame('Exp year Invalid. Format should be: yyyy. Date is expired.', $response->getMessage());
        $this->assertSame('error', $response->getStatus());
    }

    private function getValidCard()
    {
        return new CreditCard('4200000000000000', 'BEGATEWAY', 1, 2030, '123');
    }

    private function getInvalidCard()
    {
        return new CreditCard('4012001037141112', 'BEGATEWAY', 1, 2030, '123');
    }

    private function getUnauthorizedCard()
    {
        return new CreditCard('4005550000000019', 'BEGATEWAY', 10, 2030, '123');
    }

    private function getExpInvalidCard()
    {
        return new CreditCard('4200000000000000', 'BEGATEWAY', 1, 10, '123');
    }

    private function getTestRequest($secure3D = false)
    {
        $this->authorize($secure3D);

        $card = $this->getValidCard();

        $money = new Money(1233, 'EUR');

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com');
        $customer->setAddress($address);
        $customer->setIP('127.0.0.1');
        $customer->setBirthDate('1970-01-01');

        $request = new AuthorizationOperation($card, $money, $customer);
        $request->setDescription('test');
        $request->setTrackingId('my_custom_variable');
        $request->setLanguage('de');
        $request->setTestMode(true);

        $request->setAdditionalData(new AdditionalData);

        return $request;
    }
}
