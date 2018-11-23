<?php

namespace BeGateway;

use BeGateway\Response\WebhookResponse;

class WebhookTest extends TestCase
{
    public function test_WebhookIsSentWithCorrectCredentials()
    {
        $webhook = $this->getTestObjectInstance();
        $s = Settings::$shopId;
        $k = Settings::$shopKey;

        $_SERVER['PHP_AUTH_USER'] = $s;
        $_SERVER['PHP_AUTH_PW'] = $k;

        $this->assertTrue($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_WebhookIsSentWithIncorrectCredentials()
    {
        $webhook = $this->getTestObjectInstance();

        $_SERVER['PHP_AUTH_USER'] = '123';
        $_SERVER['PHP_AUTH_PW'] = '321';

        $this->assertFalse($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_WebhookIsSentWithCorrectCredentialsWhenHttpAuthorization()
    {
        $webhook = $this->getTestObjectInstance();
        $s = Settings::$shopId;
        $k = Settings::$shopKey;

        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode($s . ':' . $k);

        $this->assertTrue($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_WebhookIsSentWithCorrectCredentialsWhenRedirectHttpAuthorization()
    {
        $webhook = $this->getTestObjectInstance();
        $s = Settings::$shopId;
        $k = Settings::$shopKey;

        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode($s . ':' . $k);;

        $this->assertTrue($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_WebhookIsSentWithIncorrectCredentialsWhenHttpAuthorization()
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic QWxhZGRpbjpPcGVuU2VzYW1l';

        $webhook = $this->getTestObjectInstance();

        $this->assertFalse($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_WebhookIsSentWithIncorrectCredentialsWhenRedirectHttpAuthorization()
    {
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'Basic QWxhZGRpbjpPcGVuU2VzYW1l';

        $webhook = $this->getTestObjectInstance();

        $this->assertFalse($webhook->isAuthorized());

        $this->_clearAuthData();
    }

    public function test_RequestIsValidAndItIsSuccess()
    {
        $webhook = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage()));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isSuccess());
        $this->assertEqual($webhook->getMessage(), 'Successfully processed');
        $this->assertNotNull($webhook->getUid());
        $this->assertEqual($webhook->getPaymentMethod(), 'credit_card');
    }

    public function test_RequestIsValidAndItIsFailed()
    {
        $webhook = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage('failed')));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isFailed());
        $this->assertEqual($webhook->getMessage(), 'Payment was declined');
        $this->assertNotNull($webhook->getUid());
        $this->assertEqual($webhook->getStatus(), 'failed');
    }

    public function test_RequestIsValidAndItIsTest()
    {
        $webhook = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode($this->webhookMessage('failed', true)));

        $this->assertTrue($webhook->isValid());
        $this->assertTrue($webhook->isFailed());
        $this->assertTrue($webhook->isTest());
        $this->assertEqual($webhook->getMessage(), 'Payment was declined');
        $this->assertNotNull($webhook->getUid());
        $this->assertEqual($webhook->getStatus(), 'failed');
    }

    public function test_NotValidRequestReceived()
    {
        $webhook = $this->getTestObjectInstance();

        $reflection = new \ReflectionClass('BeGateway\Response\WebhookResponse');
        $property = $reflection->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($webhook, json_decode(''));

        $this->assertFalse($webhook->isValid());
    }

    protected function getTestObjectInstance()
    {
        self::authorizeFromEnv();

        return new WebhookResponse();
    }

    private function _clearAuthData()
    {
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
        unset($_SERVER['HTTP_AUTHORIZATION']);
        unset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
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
