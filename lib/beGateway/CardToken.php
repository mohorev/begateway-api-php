<?php
class beGateway_CardToken extends beGateway_ApiAbstract {
  public $card;

  public function __construct() {
    $this->card = new beGateway_Card();
  }

  public function submit() {
    return new beGateway_ResponseCardToken($this->_remoteRequest());
  }

  protected function _endpoint() {
    return beGateway_Settings::$gatewayBase . '/credit_cards';
  }

  protected function _buildRequestMessage() {
    $request = array(
      'request' => array(
        'holder' => $this->card->getCardHolder(),
        'number' => $this->card->getCardNumber(),
        'exp_month' => $this->card->getCardExpMonth(),
        'exp_year' => $this->card->getCardExpYear(),
        'token' => $this->card->getCardToken(),
      )
    );

    beGateway_Logger::getInstance()->write($request, beGateway_Logger::DEBUG, get_class() . '::' . __FUNCTION__);

    return $request;
  }

}
?>
