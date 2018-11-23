<?php

namespace BeGateway\Request;

use BeGateway\GatewayTransport;
use BeGateway\Language;
use BeGateway\Response;
use BeGateway\Settings;

abstract class BaseRequest
{
    protected $language;

    abstract protected function buildRequestMessage();

    abstract protected function endpoint();

    public function submit()
    {
        try {
            $response = $this->remoteRequest();
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
    protected function remoteRequest()
    {
        return GatewayTransport::submit(
            Settings::$shopId,
            Settings::$shopKey,
            $this->endpoint(),
            $this->buildRequestMessage()
        );
    }

    public function setLanguage($code)
    {
        if (in_array($code, Language::getSupportedLanguages())) {
            $this->language = $code;
        } else {
            $this->language = Language::getDefaultLanguage();
        }
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
