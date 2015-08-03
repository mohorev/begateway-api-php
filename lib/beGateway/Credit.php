<?php
class beGateway_Credit extends beGateway_ApiAbstract {
  public $card;
  public $money;
  protected $_description;
  protected $_tracking_id;

  public function __construct() {
    $this->money = new beGateway_Money();
    $this->card = new beGateway_Card();
  }

  public function setDescription($description) {
    $this->_description = $description;
  }
  public function getDescription() {
    return $this->_description;
  }

  public function setTrackingId($tracking_id) {
    $this->_tracking_id = $tracking_id;
  }
  public function getTrackingId() {
    return $this->_tracking_id;
  }

  protected function _buildRequestMessage() {
    $request = array(
      'request' => array(
        'amount' => $this->money->getCents(),
        'currency' => $this->money->getCurrency(),
        'description' => $this->getDescription(),
        'tracking_id' => $this->getTrackingId(),
        'credit_card' => array(
          'token' => $this->card->getCardToken(),
        ),
      )
    );

    beGateway_Logger::getInstance()->write($request, beGateway_Logger::DEBUG, get_class() . '::' . __FUNCTION__);

    return $request;
  }

}
?>
