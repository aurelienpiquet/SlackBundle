<?php


namespace Feelity\SlackNotifierBundle\EventSubscriber;

use Error;
use Feelity\SlackNotifierBundle\Manager\SlackManager;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class SlackExceptionHttpSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected SlackManager $slackManager,
    )
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 0],
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface || $exception instanceof NotAcceptableHttpException) {
            if ($exception->getStatusCode() === 500) {
                $this->slackManager->createHttpResponse($event);
            }
        }

        elseif ($exception->getCode() === 0) {
            try {
                $exception->getStatusCode();
            } catch (Error) {
                $this->slackManager->createHttpResponse($event);
            }

            /**
             if (!$exception->getStatusCode()) {}
             **/
        }
    }
}



