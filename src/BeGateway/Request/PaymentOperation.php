<?php

namespace BeGateway\Request;

use BeGateway\Settings;

/**
 * Request for Payment transaction.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/payment
 * @package BeGateway\Request
 */
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
