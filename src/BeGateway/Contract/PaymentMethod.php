<?php

namespace BeGateway\Contract;

interface PaymentMethod
{
    /**
     * @return string
     */
    public function name();

    /**
     * @return array
     */
    public function parameters();
}
