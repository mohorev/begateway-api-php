<?php

namespace BeGateway;

class CreditOperation extends ApiAbstract
{
    public $card;
    public $money;

    private $description;
    private $trackingId;

    public function __construct()
    {
        $this->money = new Money;
        $this->card = new Card;
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

    protected function buildRequestMessage()
    {
        $request = [
            'request' => [
                'amount' => $this->money->getCents(),
                'currency' => $this->money->getCurrency(),
                'description' => $this->getDescription(),
                'tracking_id' => $this->getTrackingId(),
                'credit_card' => [
                    'token' => $this->card->getCardToken(),
                ],
            ],
        ];

        Logger::getInstance()->write($request, Logger::DEBUG, get_class() . '::' . __FUNCTION__);

        return $request;
    }

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->getTransactionType();
    }
}
