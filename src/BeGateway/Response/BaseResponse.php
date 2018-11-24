<?php

namespace BeGateway\Response;

use BeGateway\Contract\Response;

abstract class BaseResponse implements Response
{
    protected $response;

    public function __construct($message)
    {
        $this->response = json_decode($message);
    }

    abstract public function isSuccess();

    public function isError()
    {
        if (!is_object($this->getResponse())) {
            return true;
        }

        if (isset($this->getResponse()->errors)) {
            return true;
        }

        if (isset($this->getResponse()->response)) {
            return true;
        }

        return false;
    }

    public function isValid()
    {
        return !($this->response === false || $this->response == null);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
