<?php

namespace BeGateway;

/**
 * Money is the class for Money value object.
 *
 * @package BeGateway
 */
class Money
{
    /**
     * @var int the amount expressed in the smallest
     * units of currency (eg. cents).
     */
    private $amount;
    /**
     * @var string the money currency in ISO 4217 format.
     */
    private $currency;

    /**
     * Initialize a new immutable Money object.
     *
     * To create Money object for 12.33 USD
     * you should call class construct with cents:
     *
     * new Money(1233, 'USD')
     *
     * @param int $amount
     * @param string $currency
     * @throws \Exception
     */
    public function __construct($amount, $currency)
    {
        if (!is_int($amount)) {
            throw new \Exception('Amount should be integer');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Creates a new immutable Money object.
     *
     * To create Money object for 12.33 USD
     * you should call this method:
     *
     * new Money(12.33, 'USD')
     *
     * @param float $amount
     * @param string $currency
     * @return Money
     * @throws \Exception
     */
    public static function fromFloat($amount, $currency)
    {
        $amount = intval(strval($amount * static::currencyMultiplier($currency)));

        return new static($amount, $currency);
    }

    /**
     * @return int the value represented by this object.
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string the currency ISO code.
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $code the currency ISO code.
     * @return int the the multiplier that is used to create an object from a float.
     * @see fromFloat
     */
    private static function currencyMultiplier($code)
    {
        $multipliers = (new Resource)->get('currency')['multipliers'];

        $exp = isset($multipliers['by_code'][$code])
            ? $multipliers['by_code'][$code]
            : $multipliers['default'];

        return 10 ** $exp;
    }
}
