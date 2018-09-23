<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class RecordValidationException
 * Triggered whenever a record fails Symfony Validation.
 *
 * @package Cstea\ApiBundle\Exception
 */
class RecordValidationException extends \Cstea\ApiBundle\Exception\RecordException
{
    /** @var string */
    protected $message = 'Error validating record';
    
    /** @var ConstraintViolationListInterface */
    protected $errors;

    /** @var int */
    protected $code = 2002;

    /**
     * RecordValidationException constructor.
     *
     * @param ConstraintViolationListInterface $errors   Validation errors.
     * @param \Throwable|null                  $previous Previous exception if applicable.
     */
    public function __construct(ConstraintViolationListInterface $errors, ?\Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($previous);
    }

    /**
     * Gets validation errors.
     *
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
