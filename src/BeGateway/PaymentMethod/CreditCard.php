<?php

namespace BeGateway\PaymentMethod;

use BeGateway\Contract\PaymentMethod;

class CreditCard implements PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'credit_card';
    }

    /**
     * @inheritdoc
     */
    public function parameters()
    {
        return [];
    }
}
