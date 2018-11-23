<?php

namespace BeGateway;

class Money
{
    const DEFAULT_MULTIPLIER = 2;

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
        // array currency code => multiplier
        $multipliers = [
            'BIF' => 0,
            'BYR' => 0,
            'CLF' => 0,
            'CLP' => 0,
            'CVE' => 0,
            'DJF' => 0,
            'GNF' => 0,
            'IDR' => 0,
            'IQD' => 0,
            'IRR' => 0,
            'ISK' => 0,
            'JPY' => 0,
            'KMF' => 0,
            'KPW' => 0,
            'KRW' => 0,
            'LAK' => 0,
            'LBP' => 0,
            'MMK' => 0,
            'PYG' => 0,
            'RWF' => 0,
            'SLL' => 0,
            'STD' => 0,
            'UYI' => 0,
            'VND' => 0,
            'VUV' => 0,
            'XAF' => 0,
            'XOF' => 0,
            'XPF' => 0,
            'MOP' => 1,
            'BHD' => 3,
            'JOD' => 3,
            'KWD' => 3,
            'LYD' => 3,
            'OMR' => 3,
            'TND' => 3,
        ];

        foreach ($multipliers as $currency => $multiplier) {
            if (($this->currency == $currency)) {
                return $multiplier;
            }
        }

        return self::DEFAULT_MULTIPLIER;
    }
}
