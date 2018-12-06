<?php

namespace BeGateway\Request;

use BeGateway\AdditionalData;
use BeGateway\Contract\Card as CardContract;
use BeGateway\Contract\Request;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Token;
use BeGateway\Traits\SetLanguage;
use BeGateway\Traits\IdempotentRequest;
use BeGateway\Traits\SetTestMode;

/**
 * Request for Authorization transaction.
 *
 * @see https://docs.bepaid.by/en/gateway/transactions/authorization
 * @package BeGateway\Request
 */
class AuthorizationOperation implements Request
{
    use SetLanguage, SetTestMode, IdempotentRequest;

    /**
     * @var CardContract
     */
    private $card;
    /**
     * @var Money
     */
    private $money;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var string the id of your transaction or order.
     * Max length: 255 chars.
     */
    private $trackingId;
    /**
     * @var string the order short description.
     * Max length: 255 chars.
     */
    private $description;
    /**
     * @var string url where notification about a transaction
     * will be posted to. Optional.
     */
    private $notificationUrl;
    /**
     * @var string th url on Merchant's side where bePaid will send
     * customer's browser when customer returns from 3-D Secure verification.
     * The parameter is mandatory if your merchant account is 3-D Secure enabled.
     */
    private $returnUrl;
    /**
     * @var AdditionalData the additional transaction data.
     */
    private $additionalData;

    public function __construct(CardContract $card, Money $money, Customer $customer, $trackingId)
    {
        $this->card = $card;
        $this->money = $money;
        $this->customer = $customer;
        $this->trackingId = $trackingId;
    }

    /**
     * @return CardContract
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string the order description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string the id of your transaction or order.
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * @param string $url
     */
    public function setNotificationUrl($url)
    {
        $this->notificationUrl = $url;
    }

    /**
     * @return string the Web hooks notification url.
     */
    public function getNotificationUrl()
    {
        return $this->notificationUrl;
    }

    /**
     * @param string $url
     */
    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;
    }

    /**
     * @return string the 3-D Secure return url.
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param AdditionalData $data
     */
    public function setAdditionalData(AdditionalData $data)
    {
        $this->additionalData = $data;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$gatewayBase . '/transactions/authorizations';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        $data = [
            'request' => [
                'amount' => $this->money->getAmount(),
                'currency' => $this->money->getCurrency(),
                'description' => $this->getDescription(),
                'tracking_id' => $this->getTrackingId(),
                'notification_url' => $this->getNotificationUrl(),
                'return_url' => $this->getReturnUrl(),
                'language' => $this->getLanguage(),
                'test' => $this->getTestMode(),
                'customer' => [
                    'ip' => $this->customer->getIP(),
                    'email' => $this->customer->getEmail(),
                    'birth_date' => $this->customer->getBirthDate(),
                ],
            ],
        ];

        if ($this->card instanceof Card) {
            $data['request']['credit_card'] = $this->card->toArray();
        }

        if ($this->card instanceof Token) {
            $data['request']['credit_card'] = $this->card->toArray();
        }

        if ($address = $this->customer->getAddress()) {
            $data['request']['billing_address'] = array_merge([
                'first_name' => $this->customer->getFirstName(),
                'last_name' => $this->customer->getLastName(),
                'phone' => $this->customer->getPhone(),
            ], $address->toArray());
        }

        if ($this->additionalData) {
            $data['request']['additional_data'] = $this->additionalData->toArray();
        }

        return $data;
    }
}
