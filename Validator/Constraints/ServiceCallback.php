<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Validator\Constraints;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD", "ANNOTATION"})
 */
class ServiceCallback extends \Symfony\Component\Validator\Constraint
{
    /** @var string */
    public $service;
    
    /** @var string */
    public $method;
    
    /** @var string */
    public $message;

    /**
     * ServiceCallback constructor.
     *
     * @param mixed|null $options Options.
     */
    public function __construct($options = null)
    {
        // Invocation through annotations with an array parameter only
        if (\is_array($options) && \count($options) === 1 && isset($options['value'])) {
            $options = $options['value'];
        }

        if (\is_array($options)
            && !isset($options['service'])
            && !isset($options['method'])
            && !isset($options['groups'])
        ) {
            $options = ['callback' => $options];
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
