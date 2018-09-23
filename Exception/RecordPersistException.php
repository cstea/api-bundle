<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class RecordPersistException
 * Triggered whenever saving or deleting a record fails.
 *
 * @package Cstea\ApiBundle\Exception
 */
class RecordPersistException extends \Cstea\ApiBundle\Exception\RecordException
{
    /** @var string  */
    protected $message = 'Error saving record';

    /** @var int */
    protected $code = 2001;
}
