<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Transports;

use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;
use Palet\Framework\Contracts\Logging\LoggerInterface;

class LogTransport implements MailTransportInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(MailMessageInterface $message): void
    {
        $to = implode(', ', array_map(function ($r) {
            return $r['name'] ? "{$r['name']} <{$r['address']}>" : $r['address'];
        }, $message->getTo()));

        $this->logger->debug("Sending email to {$to}: {$message->getSubject()}");
    }
}
