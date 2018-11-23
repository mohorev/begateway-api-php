<?php

namespace BeGateway\Response;

use BeGateway\Settings;

class WebhookResponse extends TransactionResponse
{
    protected $id;
    protected $key;
    protected $source = 'php://input';

    public function __construct()
    {
        parent::__construct(file_get_contents($this->source));
    }

    public function isAuthorized()
    {
        $this->processAuthData();

        return $this->id == Settings::$shopId && $this->key == Settings::$shopKey;
    }

    private function processAuthData()
    {
        $token = null;

        if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
            $this->id = $_SERVER['PHP_AUTH_USER'];
            $this->key = $_SERVER['PHP_AUTH_PW'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if ($token != null) {
            if (strpos(strtolower($token), 'basic') === 0) {
                list($this->id, $this->key) = explode(':', base64_decode(substr($token, 6)));
            }
        }
    }
}
