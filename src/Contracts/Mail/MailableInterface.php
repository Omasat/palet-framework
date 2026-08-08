<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Mail;

interface MailableInterface
{
    /**
     * Send the message using the given mailer.
     */
    public function send(MailerInterface $mailer): void;

    /**
     * Build the message.
     */
    public function build(): void;
}
