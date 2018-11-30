<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\TokenCard;

class CreditOperation implements Request
{
    public $card;
    public $money;

    private $description;
    private $trackingId;

    public function __construct(TokenCard $card, Money $money)
    {
        $this->card = $card;
        $this->money = $money;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTrackingId($trackingId)
    {
        $this->trackingId = $trackingId;
    }

    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/credits';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return [
            'request' => [
                'amount' => $this->money->getAmount(),
                'currency' => $this->money->getCurrency(),
                'description' => $this->getDescription(),
                'tracking_id' => $this->getTrackingId(),
                'credit_card' => [
                    'token' => $this->card->getToken(),
                ],
            ],
        ];
    }
}
