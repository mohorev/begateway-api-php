<?php

namespace BeGateway\Request;

use BeGateway\AdditionalData;
use BeGateway\Contract\Card;
use BeGateway\Contract\Request;
use BeGateway\CreditCard;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Settings;
use BeGateway\TokenCard;
use BeGateway\Traits\SetLanguage;
use BeGateway\Traits\IdempotentRequest;
use BeGateway\Traits\SetTestMode;

class AuthorizationOperation implements Request
{
    use SetLanguage, SetTestMode, IdempotentRequest;

    /**
     * @var CreditCard
     */
    public $card;
    /**
     * @var Money
     */
    public $money;
    /**
     * @var Customer
     */
    public $customer;

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
            $data['request']['credit_card'] = [
                'number' => $this->card->getNumber(),
                'verification_value' => $this->card->getCvc(),
                'holder' => $this->card->getHolder(),
                'exp_month' => $this->card->getExpMonth(),
                'exp_year' => $this->card->getExpYear(),
            ];
        }

        if ($this->card instanceof TokenCard) {
            $data['request']['credit_card'] = [
                'token' => $this->card->getToken(),
                'skip_three_d_secure_verification' => $this->card->getSkip3D(),
            ];
        }

        if ($address = $this->customer->getAddress()) {
            $data['request']['billing_address'] = [
                'first_name' => $this->customer->getFirstName(),
                'last_name' => $this->customer->getLastName(),
                'country' => $address->getCountry(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'zip' => $address->getZip(),
                'address' => $address->getAddress(),
                'phone' => $this->customer->getPhone(),
            ];
        }

        if ($this->additionalData) {
            $data['request']['additional_data'] = [
                'receipt_text' => $this->additionalData->getReceipt(),
                'contract' => $this->additionalData->getContract(),
            ];
        }

        return $data;
    }
}
