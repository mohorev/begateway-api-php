<?php

namespace BeGateway;

class QueryByTrackingId extends ApiAbstract
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

    protected function buildRequestMessage()
    {
        return '';
    }

    protected function endpoint()
    {
        return Settings::$gatewayBase . '/v2/transactions/tracking_id/' . $this->getTrackingId();
    }
}
