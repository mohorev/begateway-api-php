<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class CaptureOperation extends ChildTransaction
{
    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/captures';
    }
}
