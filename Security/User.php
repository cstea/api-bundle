<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security;

/**
 * Interface User
 * Extension of Symfony's UserInterface, adding support for user scopes.
 *
 * @package Cstea\ApiBundle\Security
 */
interface User extends \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * Gets scopes.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * Sets scopes.
     *
     * @param string[] $scopes Scopes.
     * @return mixed
     */
    public function setScopes(array $scopes = []);
}
