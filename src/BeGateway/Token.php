<?php

namespace BeGateway;

use BeGateway\Contract\Arrayable;
use BeGateway\Contract\Card as CardContract;

/**
 * Token is the class for data of card token.
 *
 * @package BeGateway
 */
class Token implements CardContract, Arrayable
{
    /**
     * @var string the card token you've saved from the transaction response
     * when the card was charged for the first time.
     */
    private $token;
    /**
     * @var boolean whether the 3-D Secure verification should be skipped.
     * It is useful when you, for instance, re-charge your customer
     * and you don't want that the customer passes 3-D Secure verification again.
     */
    private $skip3D;

    /**
     * Initialize a new Token.
     *
     * @param string $token
     * @param boolean $skip3D
     */
    public function __construct($token, $skip3D = false)
    {
        $this->token = (string) $token;
        $this->skip3D = (bool) $skip3D;
    }

    /**
     * @return string string the card token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return bool whether the 3-D Secure should be skipped.
     */
    public function getSkip3D()
    {
        return $this->skip3D;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            'token' => $this->token,
            'skip_three_d_secure_verification' => $this->skip3D,
        ];
    }
}
