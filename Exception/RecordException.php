<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

/**
 * Class RecordException
 *
 * @package Cstea\ApiBundle\Exception
 */
abstract class RecordException extends \Exception
{
    /** @var string **/
    protected $message = 'Record Exception';
    
    /** @var int */
    protected $code = 0;
    
    /**
     * RecordException constructor.
     *
     * @param \Throwable|null $previous Previous exception.
     * @param string|null     $message  Message.
     */
    public function __construct(?\Throwable $previous = null, ?string $message = null)
    {
        parent::__construct($message ?? $this->message, $this->code, $previous);
    }
}
