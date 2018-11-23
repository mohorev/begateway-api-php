<?php

namespace BeGateway\PaymentMethod;

class CreditCardHalvaTest extends \BeGateway\TestCase
{
    public function test_getName()
    {
        $cc = $this->getTestObject();

        $this->assertEqual($cc->name(), 'halva');
    }

    public function test_getParamsArray()
    {
        $cc = $this->getTestObject();

        $this->assertEqual($cc->parameters(), []);
    }

    public function getTestObject()
    {
        return new CreditCardHalva;
    }
}
