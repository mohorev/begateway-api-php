<?php

namespace BeGateway;

use BeGateway\Contract\GatewayTransport;
use BeGateway\Contract\Request;
use BeGateway\Response\CardTokenResponse;
use BeGateway\Response\CheckoutResponse;
use BeGateway\Response\ResponseFactory;
use BeGateway\Response\TransactionResponse;
use BeGateway\Traits\SetLanguage;
use BeGateway\Traits\SetTestMode;
use BeGateway\Transport\CurlTransport;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class ApiClient
 *
 * @see API docs: https://docs.bepaid.by/en/introduction
 * @package BeGateway
 */
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
     * Initialize a new ApiClient.
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
     * @param string $requestId the idempotent request identifier.
     * All requests with the same key will be considered attempts for the same request.
     * These keys are stored for a period of 24 hours.
     * @see https://docs.bepaid.by/en/guides/idempotent-requests
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

        return ResponseFactory::make($request, $response);
    }
}
