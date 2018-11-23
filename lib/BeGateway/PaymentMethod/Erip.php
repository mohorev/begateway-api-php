<?php

namespace BeGateway\PaymentMethod;

class Erip extends Base
{
    private $params;

    public function __construct($params)
    {
        $defaults = [
            'order_id' => null,
            'account_number' => null,
            'service_no' => null,
            'service_info' => null,
        ];

        $this->params = array_merge($defaults, $params);
    }

    public function getParamsArray()
    {
        $params = [
            'order_id' => $this->params['order_id'],
            'account_number' => $this->params['account_number'],
            'service_no' => $this->params['service_no'],
        ];

        $serviceInfo = $this->params['service_info'];

        if (is_array($serviceInfo) && !empty($serviceInfo)) {
            $params['service_info'] = $serviceInfo;
        }

        return $params;
    }
}
