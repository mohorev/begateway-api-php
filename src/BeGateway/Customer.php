<?php

namespace BeGateway;

class Customer
{
    private $ip;
    private $email;
    private $firstName;
    private $lastName;
    private $address;
    private $city;
    private $country;
    private $state;
    private $zip;
    private $phone;
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
