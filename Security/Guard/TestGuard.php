<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security\Guard;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class TestGuard
 *
 * @package Cstea\ApiBundle\Security\Guard
 */
class TestGuard implements \Symfony\Component\Security\Guard\AuthenticatorInterface
{
    // @codingStandardsIgnoreStart
    use ContainerAwareTrait;
    
    /**
     * Checks whether Authentication should be done against this Guard.
     *
     * @param Request $request Request object.
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization')
            && \preg_match('/^Bearer/', $request->headers->get('Authorization'));
    }

    /**
     * Fetches the token from the HTTP header.
     *
     * @param Request $request Request object.
     * @return mixed[]
     */
    public function getCredentials(Request $request): array
    {
        return [
            'token' => \preg_replace('/Bearer\s/', '', $request->headers->get('Authorization')),
        ];
    }

    /**
     * Fetches the user from the user provider.
     *
     * @param mixed                 $credentials  Credentials.
     * @param UserProviderInterface $userProvider User provider.
     * @return UserInterface|mixed|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials['token'] === null) {
            return null;
        }
        $username = $credentials['token'];
        
        return $userProvider->loadUserByUsername($username);
    }

    /**
     * Verifies the credentials, specifically the signature and expiration of the JWT token.
     *
     * @param mixed         $credentials Credentials.
     * @param UserInterface $user        User.
     * @return bool|mixed
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Only passes creds check if we are in test mode.
        return $this->container->get('kernel')->getEnvironment() === 'test';
    }

    /**
     * No action required upon successful authentication.
     *
     * @param Request        $request     Request object.
     * @param TokenInterface $token       Token.
     * @param string         $providerKey Provider key.
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * Throws an Unauthorized exception upon failed authentication.
     *
     * @param Request                 $request   Request object.
     * @param AuthenticationException $exception Exception.
     * @return void
     * @throws \Cstea\ApiBundle\Exception\UserUnauthorizedException Unauthorized Exception.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new \Cstea\ApiBundle\Exception\UserUnauthorizedException();
    }

    /**
     * Throws an Unauthorized exception upon starting the authentication process.
     *
     * @param Request                      $request       Request object.
     * @param AuthenticationException|null $authException Exception.
     * @return void
     * @throws \Cstea\ApiBundle\Exception\UserUnauthorizedException Unauthorized Exception.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new \Cstea\ApiBundle\Exception\UserUnauthorizedException();
    }

    /**
     * Remember me not supported for JWT token authentication.
     *
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * Return the token with the user and provider key.
     *
     * @param UserInterface $user        User.
     * @param string        $providerKey Provider key.
     * @return \Symfony\Component\Security\Guard\Token\GuardTokenInterface|PostAuthenticationGuardToken
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            ['ROLE_USER']
        );
    }
    // @codingStandardsIgnoreEnd
}
