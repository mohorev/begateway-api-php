<?php
class beGateway_QueryByToken extends beGateway_ApiAbstract {
  protected $_token;

  protected function _endpoint() {
    return beGateway_Settings::$checkoutBase . '/ctp/api/checkouts/' . $this->getToken();
  }
  public function setToken($token) {
    $this->_token = $token;
  }
  public function getToken() {
    return $this->_token;
  }
  protected function _buildRequestMessage() {
    return '';
  }

  public function submit() {
    return new beGateway_ResponseCheckout($this->_remoteRequest());
  }
}
?>
