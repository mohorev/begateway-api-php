<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Settings;

class CardToken implements Request
{
    private $number;
    private $holder;
    private $expMonth;
    private $expYear;

    public function __construct($number, $holder, $expMonth, $expYear)
    {
        $this->number = $number;
        $this->holder = $holder;
        $this->expMonth = sprintf('%02d', $expMonth);
        $this->expYear = (string) $expYear;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/credit_cards';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return [
            'request' => [
                'number' => $this->number,
                'holder' => $this->holder,
                'exp_month' => $this->expMonth,
                'exp_year' => $this->expYear,
            ],
        ];
    }
}
