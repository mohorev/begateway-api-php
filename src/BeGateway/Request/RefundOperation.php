<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Traits\IdempotentRequest;

/**
 * Request for Refund transaction.
 *
 * @see https://docs.bepaid.by/en/beyag/transactions/refund
 * @package BeGateway\Request
 */
class RefundOperation implements Request
{
    use IdempotentRequest;

    /**
     * @var Money the money to capture.
     */
    private $money;
    /**
     * @var string the UID of a parent transaction.
     */
    private $parentUid;
    /**
     * @var string the note why a refund was made.
     */
    private $reason;

    /**
     * Initialize a new RefundOperation.
     *
     * @param Money $money
     * @param string $parentUid
     * @param string $reason
     */
    public function __construct(Money $money, $parentUid, $reason)
    {
        $this->money = $money;
        $this->parentUid = $parentUid;
        $this->reason = $reason;
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
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/refunds';
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
                'reason' => $this->getReason(),
            ],
        ];
    }
}
