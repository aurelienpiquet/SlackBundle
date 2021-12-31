<?php


namespace Feelity\SlackNotifierBundle\EventSubscriber;

use Error;
use Feelity\SlackNotifierBundle\Manager\SlackManager;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SlackExceptionConsoleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected SlackManager $slackManager
    )
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::ERROR => [
                ['onConsoleError', 0]
            ]
        ];
    }

    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $exception = $event->getError();
        if ($exception->getCode() === 0) {

            try {
                $exception->getStatusCode();

            } catch (Error) {
                $this->slackManager->createCommandResponse($event, $exception);
            }
        }
    }
}



