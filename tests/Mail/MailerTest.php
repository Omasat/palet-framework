<?php

declare(strict_types=1);

namespace Tests\Mail;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\Mailer;
use Palet\Framework\Mail\Transports\ArrayTransport;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Mail\Events\MailSending;
use Palet\Framework\Mail\Events\MailSent;

class MailerTest extends TestCase
{
    public function test_mailer_sends_message_via_transport()
    {
        $transport = new ArrayTransport();
        $mailer = new Mailer($transport);
        
        $mailer->to('john@example.com')
               ->from('no-reply@example.com', 'System')
               ->send('hello_view', [], function($message) {
                   $message->subject('Hello John');
               });
               
        $messages = $transport->getMessages();
        $this->assertCount(1, $messages);
        
        $message = $messages[0];
        $this->assertEquals('Hello John', $message->getSubject());
        $this->assertEquals([['address' => 'john@example.com', 'name' => null]], $message->getTo());
        $this->assertEquals(['address' => 'no-reply@example.com', 'name' => 'System'], $message->getFrom());
    }

    public function test_mailer_dispatches_events()
    {
        $transport = new ArrayTransport();
        $mailer = new Mailer($transport);
        
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
                   ->method('dispatch')
                   ->with($this->logicalOr(
                       $this->isInstanceOf(MailSending::class),
                       $this->isInstanceOf(MailSent::class)
                   ));
                   
        $mailer->setEventDispatcher($dispatcher);
        
        $mailer->send('view_name');
    }
}
