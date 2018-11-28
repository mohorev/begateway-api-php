<?php

namespace BeGateway;

use BeGateway\Contract\Card;

/**
 * CreditCard is the class for data of credit card.
 *
 * @package BeGateway
 */
class CreditCard implements Card
{
    /**
     * @var string the card number. Length: from 12 to 19 digits.
     */
    private $number;
    /**
     * @var string the card holder name as it appears in the card.
     * Max length: 32 chars.
     */
    private $holder;
    /**
     * @var string the card expiration month expressed with two digits (e.g. 01)
     */
    private $expMonth;
    /**
     * @var string the card expiration year expressed with four digits (e.g. 2007)
     */
    private $expYear;
    /**
     * @var string the verification code.
     * 3- or 4-digit security code (called CVC2, CVV2 or CID
     * depending on the credit card brand).
     */
    private $cvc;

    /**
     * Initialize a new CreditCard.
     *
     * @param string $number
     * @param string $holder
     * @param string|int $expMonth
     * @param string|int $expYear
     * @param string $cvc
     */
    public function __construct($number, $holder, $expMonth, $expYear, $cvc)
    {
        $this->number = $number;
        $this->holder = $holder;
        $this->expMonth = sprintf('%02d', $expMonth);
        $this->expYear = (string) $expYear;
        $this->cvc = $cvc;
    }

    /**
     * @return string the card number.
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string the card holder.
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @return string the card expiration month.
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * @return string the card expiration year.
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * @return string the verification code.
     */
    public function getCvc()
    {
        return $this->cvc;
    }
}
