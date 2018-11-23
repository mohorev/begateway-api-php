<?php

namespace BeGateway;

class QueryByUid extends ApiAbstract
{
    private $uid;

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->getUid();
    }

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
}
