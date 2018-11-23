<?php

namespace BeGateway;

class AdditionalData
{
    private $receipt = [];
    private $contract = [];

    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;
    }

    public function getReceipt()
    {
        return $this->receipt;
    }

    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    public function getContract()
    {
        return $this->contract;
    }
}
