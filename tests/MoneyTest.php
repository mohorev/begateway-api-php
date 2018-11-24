<?php

namespace BeGateway\Tests;

use BeGateway\Money;

class MoneyTest extends TestCase
{
    public function testCreate()
    {
        $money = new Money;

        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame(2, Money::DEFAULT_MULTIPLIER);

        $this->assertSame(0, $money->getCents());
        $this->assertSame(0.0, $money->getAmount());
        $this->assertSame('USD', $money->getCurrency());
    }

    public function testSetAmountWithDecimals()
    {
        $money = new Money;

        $money->setAmount(10.57);
        $money->setCurrency('EUR');

        $this->assertSame(1057, $money->getCents());
        $this->assertSame(10.57, $money->getAmount());
    }

    public function testSetAmountWithoutDecimals()
    {
        $money = new Money;

        $money->setAmount(2550);
        $money->setCurrency('BYR');

        $this->assertSame(2550, $money->getCents());
        $this->assertSame(2550.0, $money->getAmount());
    }

    public function testSetCentsWithDecimals()
    {
        $money = new Money;

        $money->setCents(1057);
        $money->setCurrency('EUR');

        $this->assertSame(1057, $money->getCents());
        $this->assertSame(10.57, $money->getAmount());
    }

    public function testSetCentsWithoutDecimals()
    {
        $money = new Money;

        $money->setCents(2550);
        $money->setCurrency('JPY');

        $this->assertSame(2550, $money->getCents());
        $this->assertSame(2550.0, $money->getAmount());
    }

    public function testSet99Amount()
    {
        $money = new Money;

        $money->setAmount(20.99);
        $money->setCurrency('EUR');

        $this->assertSame(2099, $money->getCents());
        $this->assertSame(20.99, $money->getAmount());
    }

    public function testRoundAmount()
    {
        $money = new Money;

        $money->setAmount(151.2);
        $money->setCurrency('EUR');

        $this->assertSame(15120, $money->getCents());
        $this->assertSame(151.20, $money->getAmount());
    }
}
