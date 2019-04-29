<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\EventListener;

use Cstea\ApiBundle\Exception\ExceptionResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    private $responseHeaders = [];

    /** @var string */
    private $routeMatch;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * KernelResponseListener constructor.
     *
     * @param bool                $handleExceptions Enable exception handling.
     * @param string              $routeMatch       Regex pattern to match route.
     * @param mixed[]             $responseHeaders  Response headers to output.
     * @param TranslatorInterface $translator       Translator.
     */
    public function __construct(
        bool $handleExceptions = true,
        string $routeMatch = '^/',
        array $responseHeaders = [],
        TranslatorInterface $traslator
    ) {
        $this->setRouteMatch($routeMatch);
        $this->setHandleExceptions($handleExceptions);
        $this->setResponseHeaders($responseHeaders);
        $this->translator = $traslator;
    }
    
    public function setRouteMatch(string $routeMatch = '^/'): void
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * Enable of disable exception handling.
     *
     * @param bool $handleExceptions Enable exception handling.
     */
    public function setHandleExceptions(bool $handleExceptions = true): void
    {
        $this->handleExceptions = $handleExceptions;
    }

    /**
     * Set response headers.
     *
     * @param mixed $responseHeaders Response headers to output.
     */
    public function setResponseHeaders(array $responseHeaders): void
    {
        $this->responseHeaders = $responseHeaders;
    }

    /**
     * Add response headers to the existing set.
     *
     * @param mixed $responseHeaders Response headers to output.
     */
    public function addResponseHeaders(array $responseHeaders): void
    {
        $this->responseHeaders = \array_merge($this->responseHeaders, $responseHeaders);
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
        $requestUri = $event->getRequest()->getRequestUri();
        $pattern = \str_replace('/', '\/', $this->routeMatch);

        if (!$this->handleExceptions || !\preg_match('/' . $pattern . '/', $requestUri)) {
            return;
        }

        $response = (new ExceptionResponse($this->translator))($event->getException());


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
        $response->headers->add($this->responseHeaders);
    }
}
