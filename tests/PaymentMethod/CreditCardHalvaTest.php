<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\PaymentMethod\CreditCardHalva;
use PHPUnit\Framework\TestCase;

class CreditCardHalvaTest extends TestCase
{
    public function testCreate()
    {
        $payment = new CreditCardHalva;

        $this->assertInstanceOf(CreditCardHalva::class, $payment);
        $this->assertSame('halva', $payment->name());
        $this->assertSame([], $payment->parameters());
    }
}
