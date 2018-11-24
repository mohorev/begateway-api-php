<?php

namespace BeGateway\Tests\Response;

use BeGateway\Response\WebhookResponse;
use BeGateway\Settings;
use BeGateway\Tests\TestCase;

class WebhookResponseTest extends TestCase
{
    protected function tearDown()
    {
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
        unset($_SERVER['HTTP_AUTHORIZATION']);
        unset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    }

    public function testCreate()
    {
        $webhook = new WebhookResponse;

        $this->assertInstanceOf(WebhookResponse::class, $webhook);
    }

    public function testWebhookIsSentWithCorrectCredentials()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['PHP_AUTH_USER'] = Settings::$shopId;
        $_SERVER['PHP_AUTH_PW'] = Settings::$shopKey;

        $this->assertTrue($webhook->isAuthorized());
    }

    public function testWebhookIsSentWithIncorrectCredentials()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['PHP_AUTH_USER'] = '123';
        $_SERVER['PHP_AUTH_PW'] = '321';

        $this->assertFalse($webhook->isAuthorized());
    }

    public function testWebhookIsSentWithCorrectCredentialsWhenHttpAuthorization()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['HTTP_AUTHORIZATION'] = sprintf(
            'Basic %s',
            base64_encode(Settings::$shopId . ':' . Settings::$shopKey)
        );

        $this->assertTrue($webhook->isAuthorized());
    }

    public function testWebhookIsSentWithCorrectCredentialsWhenRedirectHttpAuthorization()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = sprintf(
            'Basic %s',
            base64_encode(Settings::$shopId . ':' . Settings::$shopKey)
        );

        $this->assertTrue($webhook->isAuthorized());
    }

    public function testWebhookIsSentWithIncorrectCredentialsWhenHttpAuthorization()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic QWxhZGRpbjpPcGVuU2VzYW1l';

        $this->assertFalse($webhook->isAuthorized());
    }

    public function testWebhookIsSentWithIncorrectCredentialsWhenRedirectHttpAuthorization()
    {
        $webhook = $this->getTestResponse();

        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'Basic QWxhZGRpbjpPcGVuU2VzYW1l';

        $this->assertFalse($webhook->isAuthorized());
    }

    public function testRequestIsValidAndItIsSuccess()
    {
        $webhook = $this->getTestResponse();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage()));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isSuccess());
        $this->assertSame('Successfully processed', $webhook->getMessage());
        $this->assertNotNull($webhook->getUid());
        $this->assertSame('credit_card', $webhook->getPaymentMethod());
    }

    public function testRequestIsValidAndItIsFailed()
    {
        $webhook = $this->getTestResponse();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage('failed')));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isFailed());
        $this->assertSame('Payment was declined', $webhook->getMessage());
        $this->assertNotNull($webhook->getUid());
        $this->assertSame('failed', $webhook->getStatus());
    }

    public function testRequestIsValidAndItIsTest()
    {
        $webhook = $this->getTestResponse();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage('failed', true)));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isFailed());
        $this->assertTrue($webhook->isTest());
        $this->assertSame('Payment was declined', $webhook->getMessage());
        $this->assertNotNull($webhook->getUid());
        $this->assertSame('failed', $webhook->getStatus());
    }

    public function testNotValidRequestReceived()
    {
        $webhook = $this->getTestResponse();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode(''));

        $this->assertFalse($webhook->isValid());
    }

    private function getTestResponse()
    {
        $this->authorize();

        return new WebhookResponse;
    }

    private function webhookMessage($status = 'successful', $test = true)
    {
        if ($status == 'successful') {
            $message = 'Successfully processed';
            $p_message = 'Payment was approved';
        } else {
            $message = 'Payment was declined';
            $p_message = 'Payment was declined';
        }

        return <<<EOD
{
   "transaction":{
      "customer":{
         "ip":"127.0.0.1",
         "email":"john@example.com"
      },
      "credit_card":{
         "holder":"John Doe",
         "stamp":"3709786942408b77017a3aac8390d46d77d181e34554df527a71919a856d0f28",
         "token":"d46d77d181e34554df527a71919a856d0f283709786942408b77017a3aac8390",
         "brand":"visa",
         "last_4":"0000",
         "first_1":"4",
         "exp_month":5,
         "exp_year":2015
      },
      "billing_address":{
         "first_name":"John",
         "last_name":"Doe",
         "address":"1st Street",
         "country":"US",
         "city":"Denver",
         "zip":"96002",
         "state":"CO",
         "phone":null
      },
      "payment":{
         "auth_code":"654321",
         "bank_code":"05",
         "rrn":"999",
         "ref_id":"777888",
         "message":"$p_message",
         "gateway_id":317,
         "billing_descriptor":"TEST GATEWAY BILLING DESCRIPTOR",
         "status":"$status"
      },
      "uid":"1-310b0da80b",
      "status":"$status",
      "message":"$message",
      "amount":100,
      "test":$test,
      "currency":"USD",
      "description":"Test order",
      "type":"payment",
      "payment_method_type":"credit_card"
   }
}
EOD;
    }
}
