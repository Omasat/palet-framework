<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Events;

use Palet\Framework\Contracts\Mail\MailMessageInterface;
use Throwable;

class MailFailed
{
    public readonly MailMessageInterface $message;
    public readonly Throwable $exception;

    public function __construct(MailMessageInterface $message, Throwable $exception)
    {
        $this->message = $message;
        $this->exception = $exception;
    }
}
