<?php

namespace BeGateway;

class Card
{
    private $cardNumber;
    private $cardHolder;
    private $cardExpMonth;
    private $cardExpYear;
    private $cardCvc;
    private $first1;
    private $last4;
    private $brand;
    private $cardToken;
    private $skip3D = false;

    public function setCardNumber($number)
    {
        $this->cardNumber = $number;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function setCardHolder($holder)
    {
        $this->cardHolder = $holder;
    }

    public function getCardHolder()
    {
        return $this->cardHolder;
    }

    public function setCardExpMonth($expMonth)
    {
        $this->cardExpMonth = sprintf('%02d', $expMonth);
    }

    public function getCardExpMonth()
    {
        return $this->cardExpMonth;
    }

    public function setCardExpYear($expYear)
    {
        $this->cardExpYear = $expYear;
    }

    public function getCardExpYear()
    {
        return $this->cardExpYear;
    }

    public function setCardCvc($cvc)
    {
        $this->cardCvc = $cvc;
    }

    public function getCardCvc()
    {
        return $this->cardCvc;
    }

    public function setCardToken($token)
    {
        $this->cardToken = $token;
    }

    public function getCardToken()
    {
        return $this->cardToken;
    }

    public function setSkip3D($skip = false)
    {
        $this->skip3D = $skip;
    }

    public function getSkip3D()
    {
        return $this->skip3D;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setFirst_1($digit)
    {
        $this->first1 = $digit;
    }

    public function getFirst1()
    {
        return $this->first1;
    }

    public function setLast_4($digits)
    {
        $this->last4 = $digits;
    }

    public function getLast4()
    {
        return $this->last4;
    }
}
