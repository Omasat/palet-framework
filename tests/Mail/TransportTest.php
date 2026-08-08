<?php

declare(strict_types=1);

namespace Tests\Mail;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\MailMessage;
use Palet\Framework\Mail\Transports\LogTransport;
use Palet\Framework\Mail\Transports\NullTransport;
use Palet\Framework\Contracts\Logging\LoggerInterface;

class TransportTest extends TestCase
{
    public function test_log_transport_logs_message()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
               ->method('debug')
               ->with($this->stringContains('Sending email to User <user@example.com>: Test Subject'));

        $transport = new LogTransport($logger);
        
        $message = new MailMessage();
        $message->to('user@example.com', 'User')
                ->subject('Test Subject');
                
        $transport->send($message);
    }

    public function test_null_transport_does_nothing()
    {
        $transport = new NullTransport();
        
        $message = new MailMessage();
        $message->to('user@example.com')
                ->subject('Test Subject');
                
        // Should not throw any exception or do anything
        $transport->send($message);
        
        $this->assertTrue(true);
    }
}
