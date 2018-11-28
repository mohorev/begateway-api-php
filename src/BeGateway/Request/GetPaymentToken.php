<?php

namespace BeGateway\Request;

use BeGateway\AdditionalData;
use BeGateway\Customer;
use BeGateway\Money;
use BeGateway\Settings;

class GetPaymentToken extends BaseRequest
{
    const
        TRANSACTION_TYPE_PAYMENT = 'payment',
        TRANSACTION_TYPE_TOKENIZATION = 'tokenization',
        TRANSACTION_TYPE_AUTHORIZATION = 'authorization'
    ;

    public static $version = '2.1';

    public $money;
    public $customer;

    private $description;
    private $trackingId;
    private $successUrl;
    private $declineUrl;
    private $failUrl;
    private $cancelUrl;
    private $notificationUrl;
    private $transactionType = self::TRANSACTION_TYPE_PAYMENT;
    private $readonly = [];
    private $visible = [];
    /**
     * @var \BeGateway\Contract\PaymentMethod[]
     */
    private $paymentMethods = [];
    private $expiredAt;
    /**
     * @var AdditionalData the detailed information about the payment.
     */
    private $additionalData;
    private $testMode = false;

    public function __construct(Money $money, Customer $customer)
    {
        $this->money = $money;
        $this->customer = $customer;
    }

    /**
     * @inheritdoc
     */
    public function endpoint()
    {
        return Settings::$checkoutBase . '/ctp/api/checkouts';
    }

    /**
     * @inheritdoc
     */
    public function data()
    {
        $request = [
            'checkout' => [
                'version' => self::$version,
                'transaction_type' => $this->transactionType,
                'test' => $this->getTestMode(),
                'order' => [
                    'amount' => $this->money->getAmount(),
                    'currency' => $this->money->getCurrency(),
                    'description' => $this->getDescription(),
                    'tracking_id' => $this->getTrackingId(),
                    'expired_at' => $this->getExpiryDate(),
                ],
                'settings' => [
                    'notification_url' => $this->getNotificationUrl(),
                    'success_url' => $this->getSuccessUrl(),
                    'decline_url' => $this->getDeclineUrl(),
                    'cancel_url' => $this->getCancelUrl(),
                    'fail_url' => $this->getFailUrl(),
                    'language' => $this->getLanguage(),
                    'customer_fields' => [
                        'read_only' => $this->getReadonlyFields(),
                        'visible' => $this->getVisibleFields(),
                    ],
                ],
            ],
        ];

        if ($this->additionalData) {
            $request['checkout']['order']['additional_data'] = [
                'receipt_text' => $this->additionalData->getReceipt(),
                'contract' => $this->additionalData->getContract(),
            ];
        }

        if ($this->customer) {
            $customer = [
                'email' => $this->customer->getEmail(),
                'first_name' => $this->customer->getFirstName(),
                'last_name' => $this->customer->getLastName(),
            ];

            if ($address = $this->customer->getAddress()) {
                $customer = array_merge($customer, [
                    'country' => $address->getCountry(),
                    'city' => $address->getCity(),
                    'state' => $address->getState(),
                    'zip' => $address->getZip(),
                    'address' => $address->getAddress(),
                ]);
            }

            $customer['phone'] = $this->customer->getPhone();
            $customer['birth_date'] = $this->customer->getBirthDate();

            $request['checkout']['customer'] = $customer;
        }

        $paymentMethods = $this->getPaymentMethods();

        if ($paymentMethods != null) {
            $request['checkout']['payment_method'] = $paymentMethods;
        }

        return $request;
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

    public function setSuccessUrl($url)
    {
        $this->successUrl = $url;
    }

    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    public function setDeclineUrl($url)
    {
        $this->declineUrl = $url;
    }

    public function getDeclineUrl()
    {
        return $this->declineUrl;
    }

    public function setFailUrl($url)
    {
        $this->failUrl = $url;
    }

    public function getFailUrl()
    {
        return $this->failUrl;
    }

    public function setCancelUrl($url)
    {
        $this->cancelUrl = $url;
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    public function setAuthorizationTransactionType()
    {
        $this->transactionType = self::TRANSACTION_TYPE_AUTHORIZATION;
    }

    public function setPaymentTransactionType()
    {
        $this->transactionType = self::TRANSACTION_TYPE_PAYMENT;
    }

    public function setTokenizationTransactionType()
    {
        $this->transactionType = self::TRANSACTION_TYPE_TOKENIZATION;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }

    # date when payment expires for payment
    # date is in ISO8601 format
    public function setExpiryDate($date)
    {
        $iso8601 = null;

        if ($date != null) {
            $iso8601 = date(DATE_ISO8601, strtotime($date));
        }

        $this->expiredAt = $iso8601;
    }

    public function getExpiryDate()
    {
        return $this->expiredAt;
    }

    public function setAdditionalData(AdditionalData $data)
    {
        $this->additionalData = $data;
    }

    public function getReadonlyFields()
    {
        return array_values($this->readonly);
    }

    public function getVisibleFields()
    {
        return array_values($this->visible);
    }

    public function setFirstNameReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'first_name');
    }

    public function unsetFirstNameReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['first_name']);
    }

    public function setLastNameReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'last_name');
    }

    public function unsetLastNameReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['last_name']);
    }

    public function setEmailReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'email');
    }

    public function unsetEmailReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['email']);
    }

    public function setAddressReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'address');
    }

    public function unsetAddressReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['address']);
    }

    public function setCityReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'city');
    }

    public function unsetCityReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['city']);
    }

    public function setStateReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'state');
    }

    public function unsetStateReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['state']);
    }

    public function setZipReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'zip');
    }

    public function unsetZipReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['zip']);
    }

    public function setPhoneReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'phone');
    }

    public function unsetPhoneReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['phone']);
    }

    public function setCountryReadonly()
    {
        $this->readonly = self::searchAndAdd($this->readonly, 'country');
    }

    public function unsetCountryReadonly()
    {
        $this->readonly = array_diff($this->readonly, ['country']);
    }

    public function setPhoneVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'phone');
    }

    public function unsetPhoneVisible()
    {
        $this->visible = array_diff($this->visible, ['phone']);
    }

    public function setAddressVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'address');
    }

    public function unsetAddressVisible()
    {
        $this->visible = array_diff($this->visible, ['address']);
    }

    public function setFirstNameVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'first_name');
    }

    public function unsetFirstNameVisible()
    {
        $this->visible = array_diff($this->visible, ['first_name']);
    }

    public function setLastNameVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'last_name');
    }

    public function unsetLastNameVisible()
    {
        $this->visible = array_diff($this->visible, ['last_name']);
    }

    public function setCityVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'city');
    }

    public function unsetCityVisible()
    {
        $this->visible = array_diff($this->visible, ['city']);
    }

    public function setStateVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'state');
    }

    public function unsetStateVisible()
    {
        $this->visible = array_diff($this->visible, ['state']);
    }

    public function setZipVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'zip');
    }

    public function unsetZipVisible()
    {
        $this->visible = array_diff($this->visible, ['zip']);
    }

    public function setCountryVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'country');
    }

    public function unsetCountryVisible()
    {
        $this->visible = array_diff($this->visible, ['country']);
    }

    public function setEmailVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'email');
    }

    public function unsetEmailVisible()
    {
        $this->visible = array_diff($this->visible, ['email']);
    }

    public function setBirthDateVisible()
    {
        $this->visible = self::searchAndAdd($this->visible, 'birth_date');
    }

    public function unsetBirthDateVisible()
    {
        $this->visible = array_diff($this->visible, ['birth_date']);
    }

    public function addPaymentMethod($method)
    {
        $this->paymentMethods[] = $method;
    }

    public function setTestMode($mode = true)
    {
        $this->testMode = $mode;
    }

    public function getTestMode()
    {
        return $this->testMode;
    }

    private function searchAndAdd($array, $value)
    {
        // search for $value in $array
        // if not found, adds $value to $array and returns $array
        // otherwise returns not altered $array
        $arr = $array;
        if (!in_array($value, $arr)) {
            array_push($arr, $value);
        }

        return $arr;
    }

    private function getPaymentMethods()
    {
        $result = [];

        if (!empty($this->paymentMethods)) {
            $result['types'] = [];
            foreach ($this->paymentMethods as $pm) {
                $result['types'][] = $pm->name();
                $result[$pm->name()] = $pm->parameters();
            }
        } else {
            $result = null;
        }

        return $result;
    }
}
