<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class UserUnauthorizedException
 * Triggered whenever a user is not authenticated.
 *
 * @package Cstea\ApiBundle\Exception
 */
class UserUnauthorizedException extends \Exception
{
    /** @var array */
    protected $errors = [];

    /** @var string */
    protected $message = 'Unauthorized';

    public function setErrors(array $errors): void {
        $this->errors = $errors;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
