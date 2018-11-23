<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Language;

abstract class BaseRequest implements Request
{
    protected $language;

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
