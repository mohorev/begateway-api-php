<?php

namespace BeGateway;

class AdditionalData
{
    protected $_receipt_text = [];
    protected $_contract = [];

    public function setReceipt($receipt)
    {
        $this->_receipt_text = $receipt;
    }

    public function getReceipt()
    {
        return $this->_receipt_text;
    }

    public function setContract($contract)
    {
        $this->_contract = $contract;
    }

    public function getContract()
    {
        return $this->_contract;
    }
}
