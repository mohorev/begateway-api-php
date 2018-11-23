<?php

namespace BeGateway;

class QueryByUid extends ApiAbstract
{
    private $uid;

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function getUid()
    {
        return $this->uid;
    }

    protected function buildRequestMessage()
    {
        return '';
    }

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->getUid();
    }
}
