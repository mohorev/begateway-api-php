<?php

namespace BeGateway\Tests;

use BeGateway\AdditionalData;
use BeGateway\Contract\Arrayable;

class AdditionalDataTest extends TestCase
{
    public function testCreate()
    {
        $data = new AdditionalData(['foo', 'bar'], ['recurring' => 'xxx']);

        $this->assertInstanceOf(AdditionalData::class, $data);
        $this->assertSame(['foo', 'bar'], $data->getReceipt());
        $this->assertSame(['recurring' => 'xxx'], $data->getContract());
    }

    public function testDefaults()
    {
        $data = new AdditionalData;

        $this->assertInstanceOf(AdditionalData::class, $data);
        $this->assertSame([], $data->getReceipt());
        $this->assertSame([], $data->getContract());
    }

    public function testToArray()
    {
        $data = new AdditionalData(['foo', 'bar'], ['credit' => 'xxx']);

        $this->assertInstanceOf(Arrayable::class, $data);
        $this->assertSame([
            'receipt_text' => ['foo', 'bar'],
            'contract'=> ['credit' => 'xxx'],
        ], $data->toArray());
    }
}
