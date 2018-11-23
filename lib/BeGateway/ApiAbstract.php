<?php

namespace BeGateway;

abstract class ApiAbstract
{
    protected abstract function _buildRequestMessage();

    protected $_language;

    public function submit()
    {
        try {
            $response = $this->_remoteRequest();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $response = '{ "errors":"' . $msg . '", "message":"' . $msg . '" }';
        }

        return new Response($response);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _remoteRequest()
    {
        return GatewayTransport::submit(Settings::$shopId, Settings::$shopKey, $this->_endpoint(),
            $this->_buildRequestMessage());
    }

    protected function _endpoint()
    {
        return Settings::$gatewayBase . '/transactions/' . $this->_getTransactionType();
    }

    protected function _getTransactionType()
    {
        list($module, $class) = explode('\\', get_class($this));
        $class = str_replace('Operation', '', $class);
        $class = strtolower($class) . 's';

        return $class;
    }

    public function setLanguage($language_code)
    {
        if (in_array($language_code, Language::getSupportedLanguages())) {
            $this->_language = $language_code;
        } else {
            $this->_language = Language::getDefaultLanguage();
        }
    }

    public function getLanguage()
    {
        return $this->_language;
    }
}
