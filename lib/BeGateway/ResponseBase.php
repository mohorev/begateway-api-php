<?php

namespace BeGateway;

abstract class ResponseBase
{
    protected $response;
    protected $responseArray;

    public function __construct($message)
    {
        $this->response = json_decode($message);
        $this->responseArray = json_decode($message, true);
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

    public function getResponseArray()
    {
        return $this->responseArray;
    }
}
