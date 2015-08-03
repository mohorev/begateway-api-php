<?php
class beGateway_QueryByUid extends beGateway_ApiAbstract {
  protected $_uid;

  protected function _endpoint() {
    return beGateway_Settings::$gatewayBase . '/transactions/' . $this->getUid();
  }
  public function setUid($uid) {
    $this->_uid = $uid;
  }
  public function getUid() {
    return $this->_uid;
  }
  protected function _buildRequestMessage() {
    return '';
  }
}
?>
