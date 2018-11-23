<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class VoidOperation extends ChildTransaction
{
    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/voids';
    }
}
