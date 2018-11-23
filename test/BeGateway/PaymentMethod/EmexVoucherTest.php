<?php

namespace BeGateway\PaymentMethod;

class EmexVoucherTest extends \BeGateway\TestCase
{
    public function test_getName()
    {
        $emexVoucher = $this->getTestObject();

        $this->assertEqual($emexVoucher->name(), 'emexvoucher');
    }

    public function test_getParamsArray()
    {
        $emexVoucher = $this->getTestObject();

        $this->assertEqual($emexVoucher->parameters(), []);
    }

    public function getTestObject()
    {
        return new EmexVoucher;
    }
}
