<?php

namespace BeGateway\Response;

use BeGateway\Card;

class CardTokenResponse extends BaseResponse
{
    public $card;

    public function __construct($message)
    {
        $this->card = new Card;

        parent::__construct($message);

        if ($this->isSuccess()) {
            $this->card->setCardToken($this->getResponse()->token);
            $this->card->setCardHolder($this->getResponse()->holder);
            $this->card->setCardExpMonth($this->getResponse()->exp_month);
            $this->card->setCardExpYear($this->getResponse()->exp_year);
            $this->card->setBrand($this->getResponse()->brand);
            $this->card->setFirst_1($this->getResponse()->first_1);
            $this->card->setLast_4($this->getResponse()->last_4);
        }
    }

    /**
     * @return bool whether the response is valid and response token exist
     */
    public function isSuccess()
    {
        if (!is_object($this->getResponse())) {
            return false;
        }

        return isset($this->getResponse()->token) && $this->getResponse()->token !== '';
    }
}
