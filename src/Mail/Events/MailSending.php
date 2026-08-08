<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Events;

use Palet\Framework\Contracts\Mail\MailMessageInterface;

class MailSending
{
    public readonly MailMessageInterface $message;

    public function __construct(MailMessageInterface $message)
    {
        $this->message = $message;
    }
}
