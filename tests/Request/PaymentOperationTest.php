<?php

namespace BeGateway\Tests\Request;

use BeGateway\AdditionalData;
use BeGateway\Address;
use BeGateway\Contract\Request;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Request\PaymentOperation;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class PaymentOperationTest extends TestCase
{
    public function testCreate()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(PaymentOperation::class, $request);
    }

    public function testGetSetDescription()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $description = 'Test description';
        $request->setDescription($description);
        $this->assertSame($description, $request->getDescription());
    }

    public function testGetTrackingId()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $this->assertSame('tracking_id', $request->getTrackingId());
    }


    public function testGetSetNotificationUrl()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $url = 'http://www.example.com';
        $request->setNotificationUrl($url);
        $this->assertSame($url, $request->getNotificationUrl());
    }

    public function testGetSetReturnUrl()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $url = 'http://www.example.com';
        $request->setReturnUrl($url);
        $this->assertSame($url, $request->getReturnUrl());
    }

    public function testGetSetTestMode()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $this->assertTrue($request->getTestMode());

        $request->setTestMode(false);
        $this->assertFalse($request->getTestMode());

        $request->setTestMode(true);
        $this->assertTrue($request->getTestMode());
    }

    public function testEndpoint()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $this->assertSame(Settings::$gatewayBase . '/transactions/payments', $request->endpoint());
    }

    public function testData()
    {
        $card = $this->getValidCard();
        $money = new Money(1233, 'EUR');
        $request = $this->getTestRequest($card, $money);

        $expected = [
            'request' => [
                'amount' => 1233,
                'currency' => 'EUR',
                'description' => 'test',
                'tracking_id' => 'tracking_id',
                'notification_url' => null,
                'return_url' => null,
                'language' => 'en',
                'test' => true,
                'customer' => [
                    'ip' => '127.0.0.1',
                    'email' => 'john@example.com',
                    'birth_date' => '1970-01-01',
                ],
                'credit_card' => [
                    'number' => '4200000000000000',
                    'holder' => 'BEGATEWAY',
                    'exp_month' => '01',
                    'exp_year' => '2030',
                    'verification_value' => '123',
                ],
                'billing_address' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'phone' => null,
                    'country' => 'LV',
                    'city' => 'Riga',
                    'address' => 'Demo str 12',
                    'zip' => 'LV-1082',
                    'state' => null,
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

    public function testSuccessPayment()
    {
        $this->authorize();

        $card = $this->getValidCard();
        $money = new Money(mt_rand(0, 10000), 'EUR');
        $request = $this->getTestRequest($card, $money);

        $amount = $request->getMoney()->getAmount();

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('Successfully processed', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('successful', $response->getStatus());
        $this->assertSame($amount, $response->getResponse()->transaction->amount);
    }

    public function testIncompletePayment()
    {
        $this->authorize(true);

        $card = new Card('4012001037141112', 'BEGATEWAY', 1, 2030, '123');
        $money = new Money(mt_rand(0, 10000), 'EUR');
        $request = $this->getTestRequest($card, $money);

        $amount = $request->getMoney()->getAmount();

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

    public function testFailedPayment()
    {
        $this->authorize();

        $card = new Card('4005550000000019', 'BEGATEWAY', 10, 2030, '123');
        $money = new Money(mt_rand(0, 10000), 'EUR');
        $request = $this->getTestRequest($card, $money);

        $amount = $request->getMoney()->getAmount();

        $response = $this->getApiClient()->send($request);

        $this->assertTrue($response->isValid());
        $this->assertTrue($response->isFailed());
        $this->assertSame('Payment was declined', $response->getMessage());
        $this->assertNotNull($response->getUid());
        $this->assertSame('failed', $response->getStatus());
        $this->assertSame($amount, $response->getResponse()->transaction->amount);
    }

    private function getValidCard()
    {
        return new Card('4200000000000000', 'BEGATEWAY', 1, 2030, '123');
    }

    private function getTestRequest($card, $money)
    {
        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');

        $customer = new Customer('John', 'Doe', 'john@example.com', '127.0.0.1');
        $customer->setAddress($address);
        $customer->setBirthDate('1970-01-01');

        $request = new PaymentOperation($card, $money, $customer, 'tracking_id');
        $request->setDescription('test');
        $request->setTestMode(true);

        $request->setAdditionalData(new AdditionalData);

        return $request;
    }
}
