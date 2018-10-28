<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security\Guard\Event;

use Lcobucci\JWT\Token;

class AuthenticationSuccess extends \Cstea\ApiBundle\Event\Event
{
    /** @var Token */
    private $token;

    /**
     * AuthenticationSuccess constructor.
     *
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Gets pre-authenticated token
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}
