<?php

namespace BeGateway\PaymentMethod;

class CreditCardTest extends \BeGateway\TestCase
{
    public function test_getName()
    {
        $cc = $this->getTestObject();

        $this->assertEqual($cc->name(), 'credit_card');
    }

    public function test_getParamsArray()
    {
        $cc = $this->getTestObject();

        $this->assertEqual($cc->parameters(), []);
    }

    public function getTestObject()
    {
        return new CreditCard;
    }
}
