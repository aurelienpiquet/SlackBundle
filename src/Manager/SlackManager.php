<?php


namespace Feelity\SlackNotifierBundle\Manager;


use Feelity\SlackNotifierBundle\Service\SlackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

class SlackManager
{
    private string $environment;
    private string $mode;

    public function __construct(
        protected SlackService $slackService,
    )
    {
        $this->environment = $_ENV['APP_ENV'];
        $this->slackUri = $_ENV['SLACK_NOTIFIER_URI'];
        $this->projectName = $_ENV['SLACK_NOTIFIER_NAME'];
        $this->mode = $_ENV['SLACK_NOTIFIER_MODE'];
    }

    public function isFunctionnal(): bool
    {
        if ($this->environment === $this->mode) {
            return true;
        }
        return false;
    }

    public function createHttpResponse(ExceptionEvent $event): void
    {
        if ($this->isFunctionnal()) {
            $request = Request::createFromGlobals();

            $uri = $request->server->get('REQUEST_URI');
            $method = $request->server->get('REQUEST_METHOD');
            $message = $event->getThrowable()->getMessage();

            $this->slackService->prepareHttpMessage($uri, $method, $message);
            $this->slackService->sendMessage();

        }
    }

    public function createCommandResponse(ConsoleErrorEvent $event, $exception): void
    {
        if ($this->isFunctionnal()) {
            $command = $event->getCommand()->getName();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $message = $exception->getMessage();

            $this->slackService->prepareCommandMessage($command, $file, $line, $message);
            $this->slackService->sendMessage();
        }
    }
}