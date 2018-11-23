<?php

namespace BeGateway;

class QueryByPaymentToken extends ApiAbstract
{
    private $token;

    protected function endpoint()
    {
        return Settings::$checkoutBase . '/ctp/api/checkouts/' . $this->getToken();
    }

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

    public function submit()
    {
        return new ResponseCheckout($this->remoteRequest());
    }
}
