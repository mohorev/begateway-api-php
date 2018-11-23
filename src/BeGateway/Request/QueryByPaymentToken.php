<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class QueryByPaymentToken extends BaseRequest
{
    private $token;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$checkoutBase . '/ctp/api/checkouts/' . $this->getToken();
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
