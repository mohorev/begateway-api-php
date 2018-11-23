<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class PaymentOperation extends AuthorizationOperation
{
    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/payments';
    }
}
