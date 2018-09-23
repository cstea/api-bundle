<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Trait ValidateAware
 * Provides a class the capability of validation using the Symfony validation component.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait ValidateAware
{
    
    /** @var ValidatorInterface */
    private $validator;

    /**
     * Setter injection method for the Validation interface.
     *
     * @required
     * @param ValidatorInterface $validator Validator.
     */
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * Gets the validator.
     *
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * Runs validation against an entity.
     *
     * @param \object $entity Entity to validate.
     * @throws \Cstea\ApiBundle\Exception\RecordValidationException Validation error.
     */
    protected function validate(object $entity): void
    {
        $errors = $this->validator->validate($entity);
        if (!$errors->count()) {
            return;
        }

        throw new \Cstea\ApiBundle\Exception\RecordValidationException($errors);
    }
}
