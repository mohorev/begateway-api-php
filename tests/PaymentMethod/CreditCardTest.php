<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\Contract\PaymentMethod;
use BeGateway\PaymentMethod\CreditCard;
use BeGateway\Tests\TestCase;

class CreditCardTest extends TestCase
{
    public function testCreate()
    {
        $payment = new CreditCard;

        $this->assertInstanceOf(CreditCard::class, $payment);
        $this->assertInstanceOf(PaymentMethod::class, $payment);

        $this->assertSame('credit_card', $payment->name());
        $this->assertSame([], $payment->parameters());
    }
}
