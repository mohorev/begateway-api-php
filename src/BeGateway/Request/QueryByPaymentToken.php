<?php

namespace BeGateway\Request;

use BeGateway\Contract\Request;
use BeGateway\Settings;

/**
 * Request for receive transaction information by payment token.
 *
 * @see https://docs.bepaid.by/en/checkout/query
 * @package BeGateway\Request
 */
class QueryByPaymentToken implements Request
{
    /**
     * @var string the payment token.
     */
    private $token;

    /**
     * Initialize a new QueryByPaymentToken.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$checkoutBase . '/ctp/api/checkouts/' . $this->token;
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        return null;
    }
}
