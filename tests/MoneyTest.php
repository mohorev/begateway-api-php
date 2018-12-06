<?php

namespace BeGateway\Tests;

use BeGateway\Money;

class MoneyTest extends TestCase
{
    public function testCreate()
    {
        $money = new Money(0, 'USD');

        $this->assertInstanceOf(Money::class, $money);

        $this->assertSame(0, $money->getAmount());
        $this->assertSame('USD', $money->getCurrency());
    }

    public function testCreateFromFloat()
    {
        $money = Money::fromFloat(10.57, 'EUR');

        $this->assertSame(1057, $money->getAmount());
        $this->assertSame('EUR', $money->getCurrency());
    }

    public function testCreateFromSubunits()
    {
        $money = new Money(2550, 'BYR');

        $this->assertSame(2550, $money->getAmount());
        $this->assertSame('BYR', $money->getCurrency());
    }

    public function testCreateFromFloatWith99Amount()
    {
        $money = Money::fromFloat(20.99, 'EUR');

        $this->assertSame(2099, $money->getAmount());
        $this->assertSame('EUR', $money->getCurrency());
    }

    public function testRoundFloatAmount()
    {
        $money = Money::fromFloat(151.2, 'EUR');

        $this->assertSame(15120, $money->getAmount());
        $this->assertSame('EUR', $money->getCurrency());
    }
}
