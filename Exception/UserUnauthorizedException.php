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
    /** @var string */
    protected $message = 'Unauthorized';
}
