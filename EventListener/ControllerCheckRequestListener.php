<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Cstea\ApiBundle\Security\Annotation\Scope;
use Cstea\ApiBundle\Security\User;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ControllerCheckRequestListener
 *
 * @package Cstea\ApiBundle\Listener
 */
class ControllerCheckRequestListener implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /** @var Reader */
    private $reader;
    
    /** @var User  */
    private $user;
    
    /**
     * Event subscriptions.
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }

    /**
     * ControllerCheckRequestListener constructor.
     *
     * @param Reader                $reader       Reader.
     * @param TokenStorageInterface $tokenStorage User token storage.
     */
    public function __construct(Reader $reader, TokenStorageInterface $tokenStorage)
    {
        $token = $tokenStorage->getToken();
        $this->user = $token ? $token->getUser() : null;
        $this->reader = $reader;
    }

    /**
     * Controller listener.
     *
     * @param FilterControllerEvent $event Event object.
     * @throws \ReflectionException Unknown error.
     * @throws \Cstea\ApiBundle\Exception\UserUnauthorizedException Unauthorized scope.
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controllers = $event->getController();
        if (!\is_array($controllers) || !$this->user) {
            return;
        }
        
        [$controller, $methodName] = $controllers;

        $reflectionClass = new \ReflectionClass($controller);
        /** @var Scope $classAnnotation */
        $classAnnotation = $this->reader->getClassAnnotation($reflectionClass, Scope::class);

        $reflectionObject = new \ReflectionObject($controller);
        $reflectionMethod = $reflectionObject->getMethod($methodName);
        /** @var Scope $methodAnnotation */
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, Scope::class);
        
        if (!($classAnnotation || $methodAnnotation)) {
            return;
        }
        
        $scopes = $methodAnnotation ? $methodAnnotation->getScopes() : $classAnnotation->getScopes();

        if (!\array_intersect($scopes, $this->user->getScopes())) {
            throw new \Cstea\ApiBundle\Exception\UserForbiddenException();
        }
    }
}
