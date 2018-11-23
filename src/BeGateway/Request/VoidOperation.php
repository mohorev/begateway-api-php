<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class VoidOperation extends ChildTransaction
{
    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/voids';
    }
}
