<?php

namespace BeGateway\PaymentMethod;

abstract class Base
{
    public function getName()
    {
        $name = str_replace(__NAMESPACE__ . "\\", '', get_class($this));
        $name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        return $name;
    }

    public function getParamsArray()
    {
        return [];
    }
}
