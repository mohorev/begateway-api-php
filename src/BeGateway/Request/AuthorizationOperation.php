<?php

namespace BeGateway\Request;

use BeGateway\AdditionalData;
use BeGateway\Contract\Card;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\Token;
use BeGateway\Traits\SetLanguage;
use BeGateway\Traits\IdempotentRequest;
use BeGateway\Traits\SetTestMode;

class AuthorizationOperation implements Request
{
    use SetLanguage, SetTestMode, IdempotentRequest;

    /**
     * @var CreditCard
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

    private $description;
    private $trackingId;
    private $notificationUrl;
    private $returnUrl;
    /**
     * @var AdditionalData the additional transaction data.
     */
    private $additionalData;

    public function __construct(Card $card, Money $money, Customer $customer)
    {
        $this->card = $card;
        $this->money = $money;
        $this->customer = $customer;
    }

    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setMoney(Money $money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTrackingId($trackingId)
    {
        $this->trackingId = $trackingId;
    }

    public function getTrackingId()
    {
        return $this->trackingId;
    }

    public function setNotificationUrl($url)
    {
        $this->notificationUrl = $url;
    }

    public function getNotificationUrl()
    {
        return $this->notificationUrl;
    }

    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

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

        if ($this->card instanceof CreditCard) {
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
