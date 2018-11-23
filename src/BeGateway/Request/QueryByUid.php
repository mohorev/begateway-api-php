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

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->getUid();
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
