<?php

namespace BeGateway\Request;

use BeGateway\Money;

abstract class ChildTransaction extends BaseRequest
{
    public $money;

    private $parentUid;

    public function __construct()
    {
        $this->money = new Money;
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
                'amount' => $this->money->getCents(),
            ],
        ];
    }
}
