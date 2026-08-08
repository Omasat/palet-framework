<?php

declare(strict_types=1);

namespace Tests\Mail;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Mail\Mailable;

class WelcomeEmail extends Mailable
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build(): void
    {
        $this->subject('Welcome to Palet')
             ->view('emails.welcome')
             ->text('emails.welcome_plain');
    }
}

class MailableTest extends TestCase
{
    public function test_mailable_builds_correctly()
    {
        $mail = new WelcomeEmail('Alice');
        $mail->to('alice@example.com');
        $mail->build();
        
        $this->assertEquals('Welcome to Palet', $mail->subject);
        $this->assertEquals('emails.welcome', $mail->view);
        $this->assertEquals('emails.welcome_plain', $mail->textView);
        $this->assertEquals([['address' => 'alice@example.com', 'name' => null]], $mail->to);
        
        // viewData should not be built until send() but we can check property extraction via reflection if needed
    }
}
