<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Security\Annotation;

/**
 * Annotation class for @Scope().
 *
 * @Annotation
 */
class Scope
{
    /** @var string[] */
    private $scopes = [];

    /**
     * Scope constructor.
     *
     * @param string[] $options Annotation options.
     */
    public function __construct(array $options)
    {
        $this->scopes = \is_string($options['value']) ? [$options['value']] : $options['value'];
    }

    /**
     * Gets the list of scopes.
     *
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
