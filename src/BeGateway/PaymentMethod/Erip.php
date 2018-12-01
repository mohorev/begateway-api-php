<?php

namespace BeGateway\PaymentMethod;

use BeGateway\Contract\PaymentMethod;

class Erip implements PaymentMethod
{
    private $orderId;
    private $accountNumber;
    private $serviceNo;
    private $serviceInfo;

    public function __construct($orderId, $accountNumber, $serviceNo, $serviceInfo = [])
    {
        $this->orderId = $orderId;
        $this->accountNumber = $accountNumber;
        $this->serviceNo = $serviceNo;
        $this->serviceInfo = $serviceInfo;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'erip';
    }

    /**
     * @inheritdoc
     */
    public function parameters()
    {
        $params = [
            'order_id' => $this->orderId,
            'account_number' => $this->accountNumber,
            'service_no' => $this->serviceNo,
        ];

        if ($this->serviceInfo) {
            $params['service_info'] = $this->serviceInfo;
        }

        return $params;
    }
}
