<?php

namespace BeGateway;

abstract class ChildTransaction extends ApiAbstract
{
    public $money;

    private $parentUid;

    public function __construct()
    {
        $this->money = new Money();
    }

    public function setParentUid($uid)
    {
        $this->parentUid = $uid;
    }

    public function getParentUid()
    {
        return $this->parentUid;
    }

    protected function buildRequestMessage()
    {
        $request = [
            'request' => [
                'parent_uid' => $this->getParentUid(),
                'amount' => $this->money->getCents(),
            ],
        ];

        Logger::getInstance()->write($request, Logger::DEBUG, get_class() . '::' . __FUNCTION__);

        return $request;
    }
}
