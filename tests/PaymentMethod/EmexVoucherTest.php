<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\PaymentMethod\EmexVoucher;
use PHPUnit\Framework\TestCase;

class EmexVoucherTest extends TestCase
{
    public function testCreate()
    {
        $payment = new EmexVoucher;

        $this->assertInstanceOf(EmexVoucher::class, $payment);
        $this->assertSame('emexvoucher', $payment->name());
        $this->assertSame([], $payment->parameters());
    }
}
