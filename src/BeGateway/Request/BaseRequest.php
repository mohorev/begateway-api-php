<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Resource;

abstract class BaseRequest implements Request
{
    protected $language;

    public function setLanguage($code)
    {
        $this->language = $code;
    }

    public function getLanguage()
    {
        $language = (new Resource)->get('language');

        if (in_array($this->language, $language['supported'], true)) {
            return $this->language;
        }

        return $language['default'];
    }
}
