<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Token;
use BeGateway\Traits\IdempotentRequest;

/**
 * Request for Credit transaction.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/credit
 * @package BeGateway\Request
 */
class CreditOperation implements Request
{
    use IdempotentRequest;

    private $money;
    private $token;
    private $trackingId;
    private $description;

    /**
     * Initialize a new CreditOperation.
     *
     * @param Money $money
     * @param Token $token
     * @param string $trackingId
     */
    public function __construct(Money $money, Token $token, $trackingId)
    {
        $this->money = $money;
        $this->token = $token;
        $this->trackingId = $trackingId;
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
                'tracking_id' => $this->trackingId,
                'description' => $this->description,
                'credit_card' => [
                    'token' => $this->token->getToken(),
                ],
            ],
        ];
    }
}
