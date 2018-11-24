<?php

namespace BeGateway\Tests;

use BeGateway\Customer;

class CustomerTest extends TestCase
{
    public function testCreate()
    {
        $customer = new Customer;

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function testGetSetFirstName()
    {
        $customer = new Customer;

        $customer->setFirstName('John');
        $this->assertSame('John', $customer->getFirstName());

        $customer->setFirstName('');
        $this->assertSame(null, $customer->getFirstName());
    }

    public function testGetSetLastName()
    {
        $customer = new Customer;

        $customer->setLastName('Doe');
        $this->assertSame('Doe', $customer->getLastName());

        $customer->setLastName('');
        $this->assertSame(null, $customer->getLastName());
    }

    public function testGetSetEmail()
    {
        $customer = new Customer;

        $customer->setEmail('test@example.com');
        $this->assertSame('test@example.com', $customer->getEmail());

        $customer->setEmail('');
        $this->assertSame(null, $customer->getEmail());
    }

    public function testGetSetAddress()
    {
        $customer = new Customer;

        $customer->setAddress('po box 123');
        $this->assertSame('po box 123', $customer->getAddress());

        $customer->setAddress('');
        $this->assertSame(null, $customer->getAddress());
    }

    public function testGetSetCountry()
    {
        $customer = new Customer;

        $customer->setCountry('LV');
        $this->assertSame('LV', $customer->getCountry());

        $customer->setCountry('');
        $this->assertSame(null, $customer->getCountry());
    }

    public function testGetSetZip()
    {
        $customer = new Customer;

        $customer->setZip('LV1024');
        $this->assertSame('LV1024', $customer->getZip());

        $customer->setZip('');
        $this->assertSame(null, $customer->getZip());
    }
}
