<?php

namespace BeGateway;

class Money
{
    private $amount;
    private $currency;
    private $cents;

    public function __construct($amount = 0, $currency = 'USD')
    {
        $this->currency = $currency;
        $this->setAmount($amount);
    }

    public function getCents()
    {
        if ($this->cents) {
            return $this->cents;
        }

        return intval(strval($this->amount * $this->currencyMultiplier()));
    }

    public function setCents($cents)
    {
        $this->cents = intval($cents);
        $this->amount = null;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        $this->cents = null;
    }

    public function getAmount()
    {
        if ($this->amount) {
            $amount = $this->amount;
        } else {
            $amount = $this->cents / $this->currencyMultiplier();
        }

        return floatval(strval($amount));
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    private function currencyMultiplier()
    {
        return pow(10, $this->getMultiplier());
    }

    private function getMultiplier()
    {
        $multipliers = (new Resource)->get('currency')['multipliers'];

        if (isset($multipliers['by_code'][$this->currency])) {
            return $multipliers['by_code'][$this->currency];
        }

        return $multipliers['default'];
    }
}
