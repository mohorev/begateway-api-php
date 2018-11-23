<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class QueryByUid extends BaseRequest
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
