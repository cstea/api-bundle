<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class UserForbiddenException
 * Triggered whenever a user is denied access due to a missing scope.
 *
 * @package Cstea\ApiBundle\Exception
 */
class UserForbiddenException extends \Exception
{
    /** @var string */
    protected $message = 'Forbidden';
}
