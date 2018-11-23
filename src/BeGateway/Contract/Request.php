<?php

namespace BeGateway\Contract;

interface Request
{
    /**
     * @return string the API endpoint url.
     */
    public function endpoint();

    /**
     * @return array|null the request data or NULL.
     */
    public function data();
}
