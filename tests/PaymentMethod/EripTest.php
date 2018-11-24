<?php

namespace BeGateway\Tests\PaymentMethod;

use BeGateway\PaymentMethod\Erip;
use BeGateway\Tests\TestCase;

class EripTest extends TestCase
{
    public function testCreate()
    {
        $payment = new Erip([
            'order_id' => 100001,
            'account_number' => '1234',
            'service_no' => '99999999',
            'service_info' => ['Test payment'],
        ]);

        $this->assertInstanceOf(Erip::class, $payment);
        $this->assertSame('erip', $payment->name());
        $this->assertSame([
            'order_id' => 100001,
            'account_number' => '1234',
            'service_no' => '99999999',
            'service_info' => ['Test payment'],
        ], $payment->parameters());
    }
}
