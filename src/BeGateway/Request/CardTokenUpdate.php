<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class CardTokenUpdate extends BaseRequest
{
    private $token;
    private $holder;
    private $expMonth;
    private $expYear;

    public function __construct($token, $holder = null, $expMonth = null, $expYear = null)
    {
        $this->token = $token;
        $this->holder = $holder;
        $this->expMonth = $expMonth ? sprintf('%02d', $expMonth) : null;
        $this->expYear = $expYear ? (string) $expYear : null;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/credit_cards/' . $this->token;
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return [
            'request' => [
                'holder' => $this->holder,
                'exp_month' => $this->expMonth,
                'exp_year' => $this->expYear,
            ],
        ];
    }
}
