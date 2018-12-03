<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Traits\IdempotentRequest;

abstract class ChildTransaction implements Request
{
    use IdempotentRequest;

    public $money;
    private $parentUid;

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public function setParentUid($uid)
    {
        $this->parentUid = $uid;
    }

    public function getParentUid()
    {
        return $this->parentUid;
    }

    public function data()
    {
        return [
            'request' => [
                'parent_uid' => $this->getParentUid(),
                'amount' => $this->money->getAmount(),
            ],
        ];
    }
}
