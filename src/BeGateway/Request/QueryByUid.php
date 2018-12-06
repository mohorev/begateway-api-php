<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Settings;

/**
 * Request for receive transaction information by UID.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/query
 * @package BeGateway\Request
 */
class QueryByUid implements Request
{
    /**
     * @var string the transaction UID that will be used to search for transaction.
     */
    private $uid;

    /**
     * Initialize a new QueryByUid.
     *
     * @param string $uid
     */
    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->uid;
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
