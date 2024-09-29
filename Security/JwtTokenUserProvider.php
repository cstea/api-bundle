<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class JwtTokenUserProvider
 * User provider that generates user objects from that JWT token payload.
 *
 * @package Cstea\ApiBundle\Security
 */
class JwtTokenUserProvider implements \Symfony\Component\Security\Core\User\UserProviderInterface
{
    /**
     * Resolves the username from the JWT token ino a user object.
     *
     * @param mixed|string $username User name.
     * @return UserInterface|mixed
     */
    public function loadUserByUsername($username)
    {
        $user = new JwtTokenUser();
        try {
            $reflectionClass = new \ReflectionClass(JwtTokenUser::class);
            $prop = $reflectionClass->getProperty('username');
            $prop->setAccessible(true);
            $prop->setValue($user, $username);
            $prop->setAccessible(false);
        } catch (\ReflectionException $exception) {
        }
        
        return $user;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    /**
     * Determines whether the provided class should be handled by this provider.
     *
     * @param mixed|string $class Class name.
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === User::class;
    }

    /**
     * Generates a new user object from an existing object.
     *
     * @param UserInterface $user User object.
     * @return UserInterface|mixed|null
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }
}
