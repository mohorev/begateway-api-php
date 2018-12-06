<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Traits\IdempotentRequest;

/**
 * Request for Void transaction.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/void
 * @package BeGateway\Request
 */
class VoidOperation implements Request
{
    use IdempotentRequest;

    /**
     * @var Money the money to capture.
     */
    private $money;
    /**
     * @var string the UID of a authorization transaction.
     */
    private $parentUid;

    /**
     * Initialize a new VoidOperation.
     *
     * @param Money $money
     * @param string $parentUid
     */
    public function __construct(Money $money, $parentUid)
    {
        $this->money = $money;
        $this->parentUid = $parentUid;
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @return string
     */
    public function getParentUid()
    {
        return $this->parentUid;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/voids';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return [
            'request' => [
                'parent_uid' => $this->parentUid,
                'amount' => $this->money->getAmount(),
            ],
        ];
    }
}
