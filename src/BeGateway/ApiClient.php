<?php

namespace BeGateway;

use BeGateway\Contract\GatewayTransport;
use BeGateway\Contract\Request;
use BeGateway\Request\CardToken;
use BeGateway\Request\GetPaymentToken;
use BeGateway\Request\QueryByPaymentToken;
use BeGateway\Response\CardTokenResponse;
use BeGateway\Response\CheckoutResponse;
use BeGateway\Response\TransactionResponse;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @const string the base gateway url.
     */
    const BASE_GATEWAY_URL = 'https://demo-gateway.begateway.com';
    /**
     * @const string the base checkout url.
     */
    const BASE_CHECKOUT_URL = 'https://checkout.begateway.com';
    /**
     * @const string the base API url.
     */
    const BASE_API_URL = 'https://api.begateway.com';

    /**
     * @var \BeGateway\Contract\GatewayTransport
     */
    private $transport;

    /**
     * Initialize a new Api Client.
     *
     * @param \BeGateway\Contract\GatewayTransport $transport
     */
    public function __construct(GatewayTransport $transport = null)
    {
        $this->transport = new \BeGateway\Transport\CurlTransport;
    }

    /**
     * @param Request $request
     * @return TransactionResponse|CheckoutResponse|CardTokenResponse
     */
    public function send(Request $request)
    {
        $shopId = Settings::$shopId;
        $shopKey = Settings::$shopKey;

        try {
            $response = $this->transport->send($shopId, $shopKey, $request);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $response = '{ "errors":"' . $msg . '", "message":"' . $msg . '" }';
        }

        if ($request instanceof GetPaymentToken || $request instanceof QueryByPaymentToken) {
            return new CheckoutResponse($response);
        }

        if ($request instanceof CardToken) {
            return new CardTokenResponse($response);
        }

        return new TransactionResponse($response);
    }

    public function sendIdempotent(Request $request, $id)
    {
        //
    }
}
