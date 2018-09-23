<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class RecordLookupException
 * Triggered whenever a data lookup fails, such as a MySQL query or connection error.
 *
 * @package Cstea\ApiBundle\Exception
 */
class RecordLookupException extends \Cstea\ApiBundle\Exception\RecordException
{
    /** @var string */
    protected $message = 'Record lookup error';
    
    /** @var int */
    protected $code = 1001;
}
