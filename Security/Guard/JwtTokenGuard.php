<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security\Guard;

use Cstea\ApiBundle\Security\Guard\Event\AuthenticationSuccess;
use Cstea\ApiBundle\Traits\EventAware;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class JwtTokenGuard
 *
 * @package Cstea\ApiBundle\Security\Guard
 */
class JwtTokenGuard implements \Symfony\Component\Security\Guard\AuthenticatorInterface
{
    use EventAware;
    
    // @codingStandardsIgnoreStart
    /** @var string */
    private $userField;

    /** @var string */
    private $clientField;
    
    /**
     * JwtTokenGuard constructor.
     *
     * @param string $userField   JWT claim with the username.
     * @param string $clientField JWT claim with the client_id.
     */
    public function __construct(
        string $userField = 'sub',
        string $clientField = 'aud'
    ) {
        $this->userField = $userField;
        $this->clientField = $clientField;
    }

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
        $token = (new Parser())->parse((string) $credentials['token']);
        $username = $token->getClaim($this->userField);
        if (!$username) {
            $username = $token->getClaim($this->clientField);
            if ($username) {
                $username = 'client-' . $username;
            }
        }
        
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
        /** @var \Cstea\ApiBundle\Security\User $user */
        $token = (new Parser())->parse((string) $credentials['token']); // Parses from a string
        $userVerified = $user->getUsername() === $token->getClaim($this->userField)
            || $user->getUsername() === 'client-' . $token->getClaim($this->clientField);
        
        $key = \getenv('JWT_PUBLIC_KEY');
        $key = \file_exists($key) ? \file_get_contents($key) : \base64_decode($key);
        
        $tokenVerified = $token->verify(new Sha256(), $key);
        $user->setScopes($token->getClaim('scopes'));
        
        $pass = $userVerified && $tokenVerified && !$token->isExpired();
        
        if ($pass) {
            $this->triggerEvent(new AuthenticationSuccess($token));
        }
        
        return $pass;
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
