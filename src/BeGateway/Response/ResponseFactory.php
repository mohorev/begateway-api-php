<?php

namespace BeGateway\Response;

use BeGateway\Contract\Request;
use BeGateway\Request\CardToken;
use BeGateway\Request\CardTokenUpdate;
use BeGateway\Request\GetPaymentToken;
use BeGateway\Request\QueryByPaymentToken;

/**
 * Class ResponseFactory
 *
 * @package BeGateway\Response
 */
class ResponseFactory
{
    /**
     * @param Request $request
     * @param string $json
     * @return CardTokenResponse|CheckoutResponse|TransactionResponse
     */
    public static function make(Request $request, $json)
    {
        if ($request instanceof GetPaymentToken || $request instanceof QueryByPaymentToken) {
            return new CheckoutResponse($json);
        }

        if ($request instanceof CardToken || $request instanceof CardTokenUpdate) {
            return new CardTokenResponse($json);
        }

        return new TransactionResponse($json);
    }
}
