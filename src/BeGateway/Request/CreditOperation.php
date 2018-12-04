<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\TokenCard;
use BeGateway\Traits\IdempotentRequest;

class CreditOperation implements Request
{
    use IdempotentRequest;

    private $card;
    private $money;
    private $description;
    private $trackingId;

    public function __construct(TokenCard $card, Money $money)
    {
        $this->card = $card;
        $this->money = $money;
    }

    public function setCard(TokenCard $card)
    {
        $this->card = $card;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setMoney(Money $money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
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
