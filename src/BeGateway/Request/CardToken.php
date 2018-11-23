<?php

namespace BeGateway\Request;

use BeGateway\Card;
use BeGateway\Settings;

class CardToken extends BaseRequest
{
    public $card;

    public function __construct()
    {
        $this->card = new Card;
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
                'holder' => $this->card->getCardHolder(),
                'number' => $this->card->getCardNumber(),
                'exp_month' => $this->card->getCardExpMonth(),
                'exp_year' => $this->card->getCardExpYear(),
                'token' => $this->card->getCardToken(),
            ],
        ];
    }
}
