<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\Contract\PaymentMethod;
use BeGateway\PaymentMethod\Erip;
use BeGateway\Tests\TestCase;

class EripTest extends TestCase
{
    public function testCreate()
    {
        $payment = new Erip(100001, '1234', '99999999', ['Test payment']);

        $this->assertInstanceOf(Erip::class, $payment);
        $this->assertInstanceOf(PaymentMethod::class, $payment);

        $this->assertSame('erip', $payment->name());
        $this->assertSame([
            'order_id' => 100001,
            'account_number' => '1234',
            'service_no' => '99999999',
            'service_info' => ['Test payment'],
        ], $payment->parameters());
    }
}
