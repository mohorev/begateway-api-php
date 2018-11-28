<?php

namespace BeGateway\Request;

use BeGateway\AdditionalData;
use BeGateway\Card;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Settings;

class AuthorizationOperation extends BaseRequest
{
    public $card;
    public $money;
    public $customer;

    private $description;
    private $trackingId;
    private $notificationUrl;
    private $returnUrl;
    /**
     * @var AdditionalData the additional transaction data.
     */
    private $additionalData;
    private $testMode = false;

    public function __construct(Money $money, Customer $customer)
    {
        $this->card = new Card;
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

    public function setTestMode($mode = true)
    {
        $this->testMode = $mode;
    }

    public function getTestMode()
    {
        return $this->testMode;
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
                'credit_card' => [
                    'number' => $this->card->getCardNumber(),
                    'verification_value' => $this->card->getCardCvc(),
                    'holder' => $this->card->getCardHolder(),
                    'exp_month' => $this->card->getCardExpMonth(),
                    'exp_year' => $this->card->getCardExpYear(),
                    'token' => $this->card->getCardToken(),
                    'skip_three_d_secure_verification' => $this->card->getSkip3D(),
                ],
                'customer' => [
                    'ip' => $this->customer->getIP(),
                    'email' => $this->customer->getEmail(),
                    'birth_date' => $this->customer->getBirthDate(),
                ],
            ],
        ];

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
