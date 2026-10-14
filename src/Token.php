<?php

namespace Onetoweb\TrustedshopsV2;

use DateTime;
use Stringable;

/**
 * Token.
 *
 * @author Jonathan van 't Ende <jvantende@onetoweb.nl>
 * @copyright Onetoweb B.V.
 */
class Token implements Stringable
{
    /**
     * @param string $accessToken
     * @param DateTime $expiresIn
     */
    public function __construct(
        
        #[\SensitiveParameter]
        private string $accessToken,
        private DateTime $expires
    ) {
        
    }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->accessToken;
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
