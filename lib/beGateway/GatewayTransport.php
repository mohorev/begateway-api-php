<?php
class beGateway_GatewayTransport {

    public static function submit($shop_id, $shop_key, $host, $t_request) {

        $process = curl_init($host);
        $json = json_encode($t_request);

        beGateway_Logger::getInstance()->write("Request to $host", beGateway_Logger::DEBUG, get_class() );
        beGateway_Logger::getInstance()->write("with Shop Id " . beGateway_Settings::$shopId . " & Shop key " . beGateway_Settings::$shopKey, beGateway_Logger::DEBUG, get_class() );
        if (!empty($json))
          beGateway_Logger::getInstance()->write("with message " .  $json, beGateway_Logger::DEBUG, get_class());

        if (!empty($t_request)) {
          curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-type: application/json'));
          curl_setopt($process, CURLOPT_POST, 1);
          curl_setopt($process, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($process, CURLOPT_URL, $host);
        curl_setopt($process, CURLOPT_USERPWD, beGateway_Settings::$shopId . ":" . beGateway_Settings::$shopKey);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($process);
        $error = curl_error($process);
        curl_close($process);

        if ($response === false) {
          throw new Exception("cURL error " . $error);
        }

        beGateway_Logger::getInstance()->write("Response $response", beGateway_Logger::DEBUG, get_class() );
        return $response;
    }
}
?>
