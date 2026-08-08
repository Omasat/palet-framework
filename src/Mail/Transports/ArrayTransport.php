<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Transports;

use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;

class ArrayTransport implements MailTransportInterface
{
    protected array $messages = [];

    public function send(MailMessageInterface $message): void
    {
        $this->messages[] = clone $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function flush(): void
    {
        $this->messages = [];
    }
}
