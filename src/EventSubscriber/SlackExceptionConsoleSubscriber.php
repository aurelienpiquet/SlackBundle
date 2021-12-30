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
        try {
            if ($exception->getCode() === 0) {
                $this->slackManager->createCommandResponse($event, $exception);
            }
        } catch (Error) {
        }
    }


}



