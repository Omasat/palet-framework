<?php

declare(strict_types=1);

namespace Tests\Mail\Transports;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\Transports\SmtpTransport;
use Palet\Framework\Contracts\Mail\MailMessageInterface;

class SmtpTransportTest extends TestCase
{
    public function test_instantiation()
    {
        $transport = new SmtpTransport('127.0.0.1', 2525);
        $this->assertInstanceOf(SmtpTransport::class, $transport);
        // Can't easily test actual sending without a real SMTP server or stream wrapper mock.
    }
}
