<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class RefundOperation extends ChildTransaction
{
    private $reason;

    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    public function getReason()
    {
        return $this->reason;
    }

    protected function buildRequestMessage()
    {
        $request = parent::buildRequestMessage();

        $request['request']['reason'] = $this->getReason();

        return $request;
    }

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/refunds';
    }
}
