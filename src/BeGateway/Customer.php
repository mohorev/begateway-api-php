<?php

namespace BeGateway;

/**
 * Customer is the class for data of customer making a purchase at shop.
 *
 * @package BeGateway
 */
class Customer
{
    /**
     * @var string the IP address of customer.
     */
    private $ip;
    /**
     * @var string the email of customer.
     */
    private $email;
    /**
     * @var string the first name. Max length: 30 chars.
     */
    private $firstName;
    /**
     * @var string the last name. Max length: 30 chars.
     */
    private $lastName;
    /**
     * @var string the billing country in ISO 3166-1 Alpha-2 format.
     */
    private $country;
    /**
     * @var string the billing city. Max length: 60 chars.
     */
    private $city;
    /**
     * @var string the two-letter billing state only if the billing
     * address country is US or CA.
     */
    private $state;
    /**
     * @var string the billing ZIP or postal code. If country=US,
     * zip format must be NNNNN or NNNNN-NNNN. Optional.
     */
    private $zip;
    /**
     * @var string the billing address. Max length: 255 chars
     */
    private $address;
    /**
     * @var string the phone number. Max length: 100 chars
     */
    private $phone;
    /**
     * @var string the birth date in ISO 8601 format YYYY-MM-DD. Optional.
     */
    private $birthDate;

    public function setIP($ip)
    {
        $this->ip = $this->setNullIfEmpty($ip);
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function setEmail($email)
    {
        $this->email = $this->setNullIfEmpty($email);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $this->setNullIfEmpty($firstName);
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $this->setNullIfEmpty($lastName);
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setAddress($address)
    {
        $this->address = $this->setNullIfEmpty($address);
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setCity($city)
    {
        $this->city = $this->setNullIfEmpty($city);
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCountry($country)
    {
        $this->country = $this->setNullIfEmpty($country);
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setState($state)
    {
        $this->state = $this->setNullIfEmpty($state);
    }

    public function getState()
    {
        return (in_array($this->country, ['US', 'CA'])) ? $this->state : null;
    }

    public function setZip($zip)
    {
        $this->zip = $this->setNullIfEmpty($zip);
    }

    public function getZip()
    {
        return $this->zip;
    }

    public function setPhone($phone)
    {
        $this->phone = $this->setNullIfEmpty($phone);
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setBirthDate($birthDate)
    {
        $this->birthDate = $this->setNullIfEmpty($birthDate);
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    private function setNullIfEmpty($resource)
    {
        return (strlen($resource) > 0) ? $resource : null;
    }
}
