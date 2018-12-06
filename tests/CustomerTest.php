<?php

namespace BeGateway\Tests;

use BeGateway\Address;
use BeGateway\Customer;

class CustomerTest extends TestCase
{
    public function testCreate()
    {
        $customer = new Customer('John', 'Doe', 'test@example.com', '127.0.0.1');

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function testGetFirstName()
    {
        $customer = new Customer('John', 'Doe', 'test@example.com', '127.0.0.1');
        $this->assertSame('John', $customer->getFirstName());

        $customer = new Customer('', 'Doe', 'test@example.com', '127.0.0.1');
        $this->assertSame(null, $customer->getFirstName());
    }

    public function testGetLastName()
    {
        $customer = new Customer('John', 'Doe', 'test@example.com', '127.0.0.1');

        $this->assertSame('Doe', $customer->getLastName());

        $customer = new Customer('John', '', 'test@example.com', '127.0.0.1');
        $this->assertSame(null, $customer->getLastName());
    }

    public function testGetEmail()
    {
        $customer = new Customer('John', 'Doe', 'test@example.com', '127.0.0.1');
        $this->assertSame('test@example.com', $customer->getEmail());
    }

    public function testErrorEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');
        new Customer('John', 'Doe', '', '127.0.0.1');
    }

    public function testErrorIp()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IP address');
        new Customer('John', 'Doe', 'test@example.com', '');
    }

    public function testGetSetAddress()
    {
        $customer = new Customer('John', 'Doe', 'test@example.com', '127.0.0.1');

        $this->assertSame(null, $customer->getAddress());

        $address = new Address('LV', 'Riga', 'Demo str 12', 'LV-1082');
        $customer->setAddress($address);

        $this->assertSame($address, $customer->getAddress());
    }
}
