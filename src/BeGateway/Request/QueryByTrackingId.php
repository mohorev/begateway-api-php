<?php

namespace BeGateway\Request;

use BeGateway\Settings;

class QueryByTrackingId extends BaseRequest
{
    private $trackingId;

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
        return Settings::$gatewayBase . '/v2/transactions/tracking_id/' . $this->getTrackingId();
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
