<?php

namespace BeGateway;

use BeGateway\Contract\GatewayTransport;
use BeGateway\Contract\Request;
use BeGateway\Request\CardToken;
use BeGateway\Request\CardTokenUpdate;
use BeGateway\Request\GetPaymentToken;
use BeGateway\Request\QueryByPaymentToken;
use BeGateway\Response\CardTokenResponse;
use BeGateway\Response\CheckoutResponse;
use BeGateway\Response\TransactionResponse;
use BeGateway\Traits\SetLanguage;
use BeGateway\Traits\SetTestMode;
use BeGateway\Transport\CurlTransport;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

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
     * @var string the shop identifier.
     * One of the parameters used in the process of authentication.
     */
    private $shopId;
    /**
     * @var string the shop secret key.
     * One of the parameters used in the process of authentication.
     */
    private $shopKey;
    /**
     * @var string the language of your checkout page or customer.
     */
    private $language = 'en';
    /**
     * @var bool whether the test mode is enabled.
     */
    private $testMode = false;
    /**
     * @var \BeGateway\Contract\GatewayTransport
     */
    private $transport;

    /**
     * Initialize a new Api Client.
     *
     * @param array $config the array of config params to override default settings.
     * @param \BeGateway\Contract\GatewayTransport $transport
     */
    public function __construct(array $config = [], GatewayTransport $transport = null)
    {
        if (isset($config['language'])) {
            $this->language = $config['language'];
        }

        if (!empty($config['test'])) {
            $this->testMode = true;
        }

        if (!isset($config['shop_id'], $config['shop_key'])) {
            throw new \InvalidArgumentException('The "shop_id" and "shop_key" attributes are required.');
        }

        $this->shopId = $config['shop_id'];
        $this->shopKey = $config['shop_key'];

        $this->logger = new NullLogger;
        $this->transport = $transport ? $transport : new CurlTransport;
    }

    /**
     * @param GatewayTransport $transport
     */
    public function setGatewayTransport(GatewayTransport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param Request $request
     * @return TransactionResponse|CheckoutResponse|CardTokenResponse
     */
    public function send(Request $request)
    {
        if ($request instanceof SetLanguage) {
            $request->setLanguage($this->language);
        }

        if ($request instanceof SetTestMode) {
            $request->setTestMode($this->testMode);
        }

        $this->transport->setLogger($this->logger);

        try {
            $response = $this->transport->send($this->shopId, $this->shopKey, $request);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $response = '{ "errors":"' . $msg . '", "message":"' . $msg . '" }';
        }

        if ($request instanceof GetPaymentToken || $request instanceof QueryByPaymentToken) {
            return new CheckoutResponse($response);
        }

        if ($request instanceof CardToken || $request instanceof CardTokenUpdate) {
            return new CardTokenResponse($response);
        }

        return new TransactionResponse($response);
    }

    public function sendIdempotent(Request $request, $id)
    {
        //
    }
}
