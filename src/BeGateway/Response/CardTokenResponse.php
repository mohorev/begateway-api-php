<?php

namespace BeGateway\Response;

class CardTokenResponse extends BaseResponse
{
    public $holder;
    public $brand;
    public $first1;
    public $last4;
    public $stamp;
    public $token;
    public $expMonth;
    public $expYear;

    public function __construct($message)
    {
        parent::__construct($message);

        if ($this->isSuccess()) {
            $this->holder = $this->getResponse()->holder;
            $this->brand = $this->getResponse()->brand;
            $this->first1 = $this->getResponse()->first_1;
            $this->last4 = $this->getResponse()->last_4;
            $this->stamp = $this->getResponse()->stamp;
            $this->token = $this->getResponse()->token;
            $this->expMonth = sprintf('%02d', $this->getResponse()->exp_month);
            $this->expYear = (string) $this->getResponse()->exp_year;
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
