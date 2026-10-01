<?php

namespace SMTP2GO\App;

use SMTP2GOWPPlugin\SMTP2GO\Service\Mail\Send;

class QueuedSend extends Send
{
    private $serialisedBody;

    public function __construct(array $serialisedBody)
    {
        $this->serialisedBody = $serialisedBody;
    }

    public function buildRequestBody(): array
    {
        return $this->serialisedBody;
    }

    /*
     These are used by the Logger
     */
    public function getSender(): string
    {
        return (string) ($this->serialisedBody['sender'] ?? '');
    }

    public function getRecipients(): array
    {
        return (array) ($this->serialisedBody['to'] ?? []);
    }

    public function getSubject(): string
    {
        return (string) ($this->serialisedBody['subject'] ?? '');
    }


}