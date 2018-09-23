<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraint;

/**
 * Class ServiceCallbackValidator
 * Allows to specify a service and method to call for validation.
 * Any Exception thrown by the service method is treated as a validation error.
 *
 * @package Cstea\ApiBundle\Validator\Constraints
 */
class ServiceCallbackValidator extends \Symfony\Component\Validator\ConstraintValidator
{
    
    use ContainerAwareTrait;

    /**
     * Runs validator by calling the specified service method.
     *
     * @param mixed      $object     Object to validate.
     * @param Constraint $constraint Constraint object.
     */
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof ServiceCallback) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException(
                $constraint,
                __NAMESPACE__ . '\Callback'
            );
        }
        
        if ($object === null) {
            return;
        }
        
        $method = $constraint->method;
        
        try {
            $service = $this->container->get($constraint->service);
        } catch (\Throwable $exception) {
            throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException(
                'Service not found',
                0,
                $exception
            );
        }

        if (!$service || !\method_exists($service, $method)) {
            throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException(
                'Service callback not found'
            );
        }

        try {
            // Run the method. Any exception is converted into a violation error.
            $service->$method($this->context->getObject());
        } catch (\Throwable $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
        
        return;
    }
}
