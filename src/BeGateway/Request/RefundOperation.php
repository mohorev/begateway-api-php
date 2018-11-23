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

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/refunds';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        $request = parent::data();
        $request['request']['reason'] = $this->getReason();

        return $request;
    }
}
