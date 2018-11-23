<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class PaymentOperation extends AuthorizationOperation
{
    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/payments';
    }
}
