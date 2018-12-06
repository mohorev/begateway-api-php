<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\Contract\PaymentMethod;
use BeGateway\PaymentMethod\CreditCardHalva;
use BeGateway\Tests\TestCase;

class CreditCardHalvaTest extends TestCase
{
    public function testCreate()
    {
        $payment = new CreditCardHalva;

        $this->assertInstanceOf(CreditCardHalva::class, $payment);
        $this->assertInstanceOf(PaymentMethod::class, $payment);

        $this->assertSame('halva', $payment->name());
        $this->assertSame([], $payment->parameters());
    }
}
