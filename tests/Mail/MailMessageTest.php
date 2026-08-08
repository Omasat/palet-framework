<?php

declare(strict_types=1);

namespace Tests\Mail;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\MailMessage;

class MailMessageTest extends TestCase
{
    public function test_sets_properties_correctly()
    {
        $message = new MailMessage();
        
        $message->subject('Welcome!')
                ->from('info@example.com', 'Info')
                ->to('user@example.com', 'User')
                ->cc('manager@example.com', 'Manager')
                ->bcc('admin@example.com')
                ->html('<p>Hello</p>')
                ->text('Hello')
                ->attach('/tmp/file.pdf', ['mime' => 'application/pdf']);

        $this->assertEquals('Welcome!', $message->getSubject());
        $this->assertEquals(['address' => 'info@example.com', 'name' => 'Info'], $message->getFrom());
        $this->assertEquals([['address' => 'user@example.com', 'name' => 'User']], $message->getTo());
        $this->assertEquals([['address' => 'manager@example.com', 'name' => 'Manager']], $message->getCc());
        $this->assertEquals([['address' => 'admin@example.com', 'name' => null]], $message->getBcc());
        $this->assertEquals('<p>Hello</p>', $message->getHtml());
        $this->assertEquals('Hello', $message->getText());
        $this->assertCount(1, $message->getAttachments());
        $this->assertEquals('/tmp/file.pdf', $message->getAttachments()[0]['file']);
    }
}
