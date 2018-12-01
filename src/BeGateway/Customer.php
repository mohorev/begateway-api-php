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
     * @var string the first name. Max length: 30 chars.
     */
    private $firstName;
    /**
     * @var string the last name. Max length: 30 chars.
     */
    private $lastName;
    /**
     * @var string the email of customer.
     */
    private $email;
    /**
     * @var Address the billing address.
     */
    private $address;
    /**
     * @var string the IPv4 address of customer.
     */
    private $ip;
    /**
     * @var string the phone number. Max length: 100 chars
     */
    private $phone;
    /**
     * @var string the birth date in ISO 8601 format YYYY-MM-DD. Optional.
     */
    private $birthDate;

    /**
     * Initialize a new Customer.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $ip
     */
    public function __construct($firstName, $lastName, $email, $ip)
    {
        $this->firstName = $this->setNullIfEmpty($firstName);
        $this->lastName = $this->setNullIfEmpty($lastName);
        $this->email = $email;
        $this->ip = $ip;

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        if (filter_var($this->ip, FILTER_VALIDATE_IP) === false) {
            throw new \InvalidArgumentException('Invalid IP address');
        }
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
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
