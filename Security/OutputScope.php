<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security;

/**
 * Class OutputScope
 * This class reconciles user scopes (permissions) with serialization groups in the entities.
 * The purpose is to use serialization groups to restrict data points and only return them when
 * theses data points are requested through the query string, and use logged user is allowed to see them.
 * 
 * @package Cstea\ApiBundle\Security
 */
abstract class OutputScope
{
    private const SCOPE_DEFAULT = 'default';
    public const USER_ALLOW_ALL = 'allow_all';

    /** @var string[] */
    protected $registeredScopes = [];
    
    /** @var string[]  */
    private $requestedScopes = [self::SCOPE_DEFAULT];
    
    /** @var User */
    private $user;
    
    /**
     * OutputScope constructor.
     *
     * @param User|null $user            User entity.
     * @param string[]  $requestedScopes Scopes.
     */
    public function __construct(?User $user = null, array $requestedScopes = [])
    {
        $this->user = $user;
        foreach ($requestedScopes as $scope) {
            $this->requestScope($scope);
        }
    }
    
    /**
     * Requests a scope for the JSON response output.
     *
     * @param string $scope Scope.
     */
    public function requestScope(string $scope): void
    {
        if (\array_key_exists($scope, $this->registeredScopes)) {
            $this->requestedScopes[] = $scope;
        }
        
        return;
    }

    /**
     * Fetches the allowed scopes for output.
     * This function removes any requested scopes that the user lacks access to.
     *
     * @return string[]
     */
    public function getScopes(): array
    {
        // If no user is specified, scopes cannot be verified
        // so only return the default scope.
        if (!$this->user) {
            return [self::SCOPE_DEFAULT];
        }
        
        return \array_filter($this->requestedScopes, function ($scope) {
           return $scope === self::SCOPE_DEFAULT
               || $this->registeredScopes[$scope] === self::USER_ALLOW_ALL
               || \in_array($this->registeredScopes[$scope], $this->user->getScopes());
        });
    }
}
