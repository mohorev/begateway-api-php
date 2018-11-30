<?php

namespace BeGateway\Traits;

use BeGateway\Resource;

trait SetLanguage
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param string $code
     */
    public function setLanguage($code)
    {
        $this->language = $code;
    }

    /**
     * @return string the language code.
     */
    public function getLanguage()
    {
        $language = (new Resource)->get('language');

        if (in_array($this->language, $language['supported'], true)) {
            return $this->language;
        }

        return $language['default'];
    }
}
