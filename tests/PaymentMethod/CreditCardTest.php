<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\PaymentMethod\CreditCard;
use PHPUnit\Framework\TestCase;

class CreditCardTest extends TestCase
{
    public function testCreate()
    {
        $payment = new CreditCard;

        $this->assertInstanceOf(CreditCard::class, $payment);
        $this->assertSame('credit_card', $payment->name());
        $this->assertSame([], $payment->parameters());
    }
}
