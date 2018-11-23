<?php

namespace BeGateway;

class QueryByTrackingId extends ApiAbstract
{
    private $trackingId;

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/v2/transactions/tracking_id/' . $this->getTrackingId();
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
        return '';
    }
}
