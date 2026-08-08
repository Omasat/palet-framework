<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Mail;

interface MailTransportInterface
{
    /**
     * Send the given mail message.
     */
    public function send(MailMessageInterface $message): void;
}
