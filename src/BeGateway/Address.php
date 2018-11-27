<?php

namespace BeGateway;

/**
 * Address is the class for data of billing address.
 *
 * @package BeGateway
 */
class Address
{
    /**
     * @var string the billing country in ISO 3166-1 Alpha-2 format.
     */
    private $country;
    /**
     * @var string the billing city. Max length: 60 chars.
     */
    private $city;
    /**
     * @var string the billing address. Max length: 255 chars
     */
    private $address;
    /**
     * @var string|null the billing ZIP or postal code. If country=US,
     * zip format must be NNNNN or NNNNN-NNNN. Optional.
     */
    private $zip;
    /**
     * @var string|null the two-letter billing state only if the billing
     * address country is US or CA. Optional.
     */
    private $state;

    /**
     * Initialize a new Address.
     *
     * @param string $country
     * @param string $city
     * @param string $address
     * @param string $zip
     * @param string $state
     */
    public function __construct($country, $city, $address, $zip = null, $state = null)
    {
        $this->country = $country;
        $this->city = $city;
        $this->address = $address;
        $this->zip = $zip;

        if (in_array($this->country, ['US', 'CA'], true)) {
            $this->state = $state;
        }
    }

    /**
     * @return string the billing country.
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string billing city.
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string the billing address.
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string|null the billing ZIP or postal code.
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string|null the two-letter billing state.
     */
    public function getState()
    {
        return $this->state;
    }
}
