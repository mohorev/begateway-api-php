<?php

namespace BeGateway;

/**
 * AdditionalData is the class for additional transaction data.
 *
 * @package BeGateway
 */
class AdditionalData
{
    /**
     * @var array the array of strings that will be added to client's mail.
     */
    private $receipt = [];
    /**
     * @var array the array consisting of elements:
     * - `recurring`: the card token to use it in subsequent charges without to enter a card data again.
     * - `oneclick`: the card token to use it in the oneclick payment scheme.
     * - `credit`: the card token to use it in operations credit and payout.
     */
    private $contract = [];

    public function __construct(array $receipt = [], array $contract = [])
    {
        $this->receipt = $receipt;
        $this->contract = $contract;
    }

    /**
     * @return array the list of receipts.
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @return array
     * @see $contract
     */
    public function getContract()
    {
        return $this->contract;
    }
}
