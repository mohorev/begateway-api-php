<?php

namespace BeGateway\Traits;

trait SetTestMode
{
    /**
     * @var bool
     */
    private $testMode = false;

    /**
     * @param bool $mode `true` if test mode enabled, `false` otherwise.
     */
    public function setTestMode($mode)
    {
        $this->testMode = $mode;
    }

    /**
     * @return  bool whether the test mode is enabled.
     */
    public function getTestMode()
    {
        return $this->testMode;
    }
}
