<?php

namespace BeGateway\PaymentMethod;

use BeGateway\Contract\PaymentMethod;

class EmexVoucher implements PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'emexvoucher';
    }

    /**
     * @inheritdoc
     */
    public function parameters()
    {
        return [];
    }
}
