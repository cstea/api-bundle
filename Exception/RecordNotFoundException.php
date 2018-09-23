<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class RecordNotFoundException
 * Triggered whenever a single record lookup returns an empty result set.
 *
 * @package Cstea\ApiBundle\Exception
 */
class RecordNotFoundException extends \Cstea\ApiBundle\Exception\RecordException
{
    /** @var string */
    protected $message = 'Record not found';

    /** @var int */
    protected $code = 1002;
}
