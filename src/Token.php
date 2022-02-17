<?php

namespace Onetoweb\TrustedshopsV2;

use DateTime;

/**
 * Token.
 *
 * @author Jonathan van 't Ende <jvantende@onetoweb.nl>
 * @copyright Onetoweb B.V.
 */
class Token
{
    /**
     * @param string $accessToken
     * @param DateTime $expiresIn
     */
    public function __construct(string $accessToken, DateTime $expires)
    {
        $this->accessToken = $accessToken;
        $this->expires = $expires;
    }
    
    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
    
    /**
     * @return DateTime
     */
    public function getExpires(): DateTime
    {
        return $this->expires;
    }
    
    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires < new DateTime();
    }
}