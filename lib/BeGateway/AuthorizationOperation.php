<?php

namespace BeGateway;

class AuthorizationOperation extends ApiAbstract
{
    public $card;
    public $money;
    public $customer;
    public $additionalData;

    private $description;
    private $trackingId;
    private $notificationUrl;
    private $returnUrl;
    private $testMode = false;

    public function __construct()
    {
        $this->customer = new Customer;
        $this->money = new Money;
        $this->card = new Card;
        $this->additionalData = new AdditionalData;
        $this->language = Language::getDefaultLanguage();
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

    protected function buildRequestMessage()
    {
        $request = [
            'request' => [
                'amount' => $this->money->getCents(),
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
                'billing_address' => [
                    'first_name' => $this->customer->getFirstName(),
                    'last_name' => $this->customer->getLastName(),
                    'country' => $this->customer->getCountry(),
                    'city' => $this->customer->getCity(),
                    'state' => $this->customer->getState(),
                    'zip' => $this->customer->getZip(),
                    'address' => $this->customer->getAddress(),
                    'phone' => $this->customer->getPhone(),
                ],
                'additional_data' => [
                    'receipt_text' => $this->additionalData->getReceipt(),
                    'contract' => $this->additionalData->getContract(),
                ],
            ],
        ];

        Logger::getInstance()->write($request, Logger::DEBUG, get_class() . '::' . __FUNCTION__);

        return $request;
    }

}
