<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security;

/**
 * Class ApiUser
 * UserInterface implementation containing the JWT token authenticated user.
 *
 * @package Cstea\ApiBundle\Security
 */
class JwtTokenUser implements \Cstea\ApiBundle\Security\User
{
    /** @var string */
    private $username = '';
    
    /** @var string[] */
    private $scopes = [];

    /**
     * Gets salt.
     *
     * @return string
     */
    public function getSalt(): string
    {
        return '';
    }

    /**
     * Gets user roles.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * Gets user scopes.
     *
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Sets user scopes.
     *
     * @param string[] $scopes Scopes.
     */
    public function setScopes(array $scopes = []): void
    {
        $this->scopes = $scopes;
    }

    /**
     * Random password. This feature is not needed for tokens.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return \md5((string) \rand());
    }

    /**
     * Gets username from the token.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * Erase credentials
     */
    public function eraseCredentials(): void
    {
        return;
    }
}
