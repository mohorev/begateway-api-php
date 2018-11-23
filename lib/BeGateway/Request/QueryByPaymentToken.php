<?php

namespace BeGateway\Request;

use BeGateway\Response\CheckoutResponse;
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

    protected function buildRequestMessage()
    {
        return '';
    }

    protected function endpoint()
    {
        return Settings::$checkoutBase . '/ctp/api/checkouts/' . $this->getToken();
    }

    public function submit()
    {
        return new CheckoutResponse($this->remoteRequest());
    }
}
