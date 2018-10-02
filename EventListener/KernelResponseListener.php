<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\EventListener;

use Cstea\ApiBundle\Exception\ExceptionResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class KernelResponseListener
 *
 * @package Cstea\ApiBundle\Listener
 */
class KernelResponseListener implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /** @var bool */
    private $handleExceptions;

    /** @var mixed[] */
    private $outputHeaders = [];

    /**
     * KernelResponseListener constructor.
     *
     * @param bool $handleExceptions
     */
    public function __construct(bool $handleExceptions = true, array $outputHeaders = [])
    {
        $this->handleExceptions = $handleExceptions;
        $this->outputHeaders = $outputHeaders;
    }

    /**
     * List of all subscribed events.
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
            'kernel.response' => 'onKernelResponse',
        ];
    }

    /**
     * Event listener for Kernel exceptions.
     * Returns an HTTP Response for all HttpExceptions thrown in controllers.
     *
     * @param GetResponseForExceptionEvent $event Event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if (!$this->handleExceptions) {
            return;
        }

        $response = (new ExceptionResponse())($event->getException());


        if ($response) {
            $event->setResponse($response);
            return;
        }
    }

    /**
     * Adds CORS response header.
     *
     * @param FilterResponseEvent $event Event object.
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->add($this->outputHeaders);
    }
}
