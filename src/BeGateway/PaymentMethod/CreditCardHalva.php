<?php

namespace BeGateway\PaymentMethod;

use BeGateway\Contract\PaymentMethod;

class CreditCardHalva implements PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'halva';
    }

    /**
     * @inheritdoc
     */
    public function parameters()
    {
        return [];
    }
}
