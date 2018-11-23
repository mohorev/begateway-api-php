<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class CaptureOperation extends ChildTransaction
{
    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/captures';
    }
}
