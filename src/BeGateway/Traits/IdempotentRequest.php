<?php

namespace BeGateway\Traits;

trait IdempotentRequest
{
    /**
     * @var string the unique request identifier.
     */
    private $id;

    /**
     * @param string $id the request ID for idempotent requests.
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return string the request ID.
     */
    public function getId()
    {
        return $this->id;
    }
}
