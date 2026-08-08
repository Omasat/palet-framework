<?php

declare(strict_types=1);

namespace Tests\Mail\Transports;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\Transports\SendmailTransport;

class SendmailTransportTest extends TestCase
{
    public function test_instantiation()
    {
        $transport = new SendmailTransport('/usr/sbin/sendmail -bs');
        $this->assertInstanceOf(SendmailTransport::class, $transport);
    }
}
