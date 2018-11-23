<?php

namespace BeGateway;

abstract class ApiAbstract
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

    protected function getTransactionType()
    {
        list($module, $class) = explode('\\', get_class($this));
        $class = str_replace('Operation', '', $class);
        $class = strtolower($class) . 's';

        return $class;
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
