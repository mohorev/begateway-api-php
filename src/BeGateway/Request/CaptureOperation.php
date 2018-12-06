<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Traits\IdempotentRequest;

/**
 * Request for Capture transaction.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/capture
 * @package BeGateway\Request
 */
class CaptureOperation implements Request
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
     * Initialize a new CaptureOperation.
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
        return Settings::$gatewayBase . '/transactions/captures';
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
