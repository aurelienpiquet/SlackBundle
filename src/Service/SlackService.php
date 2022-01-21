<?php

declare(strict_types=1);
namespace Feelity\SlackNotifierBundle\Service;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpClient\HttpClient;

class SlackService
{
    protected array $preparedMessage;

    protected string $uri;

    protected string $project;

    public function __construct(
        string $uri,
        string $project,
    )
    {
        $this->uri = $uri;
        $this->project = $project;
    }

    public function prepareCommandMessage($command, $file, $line, $message): void
    {
        $this->preparedMessage = ["blocks" => [
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*Project :* " . $this->project ."\nYou have an error 500 in " . $file . " on line " . $line
                ],
            ],
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*Command :* ". $command ."\n*Message :*\n" . $message
                ],
            ]
        ]
        ];
    }

    public function prepareHttpMessage(string $uri, string $method, string $message): void
    {
        $this->preparedMessage = ["blocks" => [
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*Project :* " . $this->project ."\nYou have an error 500 in " . $uri
                ],
            ],
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*Method :* ". $method ."\n*Message :*\n" . $message
                ],
            ]
        ]
        ];
    }

    public function sendMessage(): void
    {
        $client = HttpClient::create();
        $response = $client->request('POST', 'https://hooks.slack.com/services/' . $this->uri, [
            'json' => $this->preparedMessage,
        ]);

        if ($response->getStatusCode() != 200 || !$this->uri) {
            throw new Exception("The message couldn't be send, check your SLACK_NOTIFIER_URI in your .env or Slack is currently offline");
        }

    }
}
