<?php

namespace BeGateway\Request;

use BeGateway\Card;
use BeGateway\Logger;
use BeGateway\ResponseCardToken;
use BeGateway\Settings;

class CardToken extends BaseRequest
{
    public $card;

    public function __construct()
    {
        $this->card = new Card;
    }

    public function submit()
    {
        return new ResponseCardToken($this->remoteRequest());
    }

    protected function buildRequestMessage()
    {
        $request = [
            'request' => [
                'holder' => $this->card->getCardHolder(),
                'number' => $this->card->getCardNumber(),
                'exp_month' => $this->card->getCardExpMonth(),
                'exp_year' => $this->card->getCardExpYear(),
                'token' => $this->card->getCardToken(),
            ],
        ];

        Logger::getInstance()->write($request, Logger::DEBUG, get_class() . '::' . __FUNCTION__);

        return $request;
    }

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/credit_cards';
    }
}
