<?php
class beGateway_QueryByTrackingId extends beGateway_ApiAbstract {
  protected $_tracking_id;

  protected function _endpoint() {
    return beGateway_Settings::$gatewayBase . '/transactions/tracking_id/' . $this->getTrackingId();
  }
  public function setTrackingId($tracking_id) {
    $this->_tracking_id = $tracking_id;
  }
  public function getTrackingId() {
    return $this->_tracking_id;
  }
  protected function _buildRequestMessage() {
    return '';
  }
}
?>
