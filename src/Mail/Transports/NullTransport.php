<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Transports;

use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;

class NullTransport implements MailTransportInterface
{
    public function send(MailMessageInterface $message): void
    {
        // Do nothing.
    }
}
