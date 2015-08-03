<?php
abstract class beGateway_ApiAbstract {
  protected abstract function _buildRequestMessage();
  protected $_language;

  public function submit() {
    try {
      $response = $this->_remoteRequest();
    } catch (Exception $e) {
      $msg = $e->getMessage();
      $response = '{ "errors":"' . $msg . '", "message":"' . $msg . '" }';
    }
    return new beGateway_Response($response);
  }

  protected function _remoteRequest() {
    return beGateway_GatewayTransport::submit( beGateway_Settings::$shopId, beGateway_Settings::$shopKey , $this->_endpoint(), $this->_buildRequestMessage() );
  }

  protected function _endpoint() {
    return beGateway_Settings::$gatewayBase . '/transactions/' . $this->_getTransactionType();
  }

  protected function _getTransactionType() {
    list($module,$klass) = explode('_', get_class($this));
    $klass = strtolower($klass) . 's';
    return $klass;
  }
  public function setLanguage($language_code) {
    if (in_array($language_code, beGateway_Language::getSupportedLanguages())) {
      $this->_language = $language_code;
    }else{
      $this->_language = beGateway_Language::getDefaultLanguage();
    }
  }

  public function getLanguage() {
    return $this->_language;
  }
}
?>
