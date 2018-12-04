<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Settings;

/**
 * Request for receive transaction information by tracking ID.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/query
 * @package BeGateway\Request
 */
class QueryByTrackingId implements Request
{
    /**
     * @var string tracking ID that will be used to search for transactions.
     */
    private $trackingId;

    /**
     * Initialize a new QueryByTrackingId.
     *
     * @param $trackingId
     */
    public function __construct($trackingId)
    {
        $this->trackingId = $trackingId;
    }

    /**
     * @return string
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/v2/transactions/tracking_id/' . $this->trackingId;
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
