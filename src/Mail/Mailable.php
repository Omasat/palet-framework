<?php

declare(strict_types=1);

namespace Palet\Framework\Mail;

use Palet\Framework\Contracts\Mail\MailableInterface;
use Palet\Framework\Contracts\Mail\MailerInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;
use Palet\Framework\Contracts\View\ViewFactoryInterface;
use ReflectionClass;
use ReflectionProperty;

abstract class Mailable implements MailableInterface
{
    public array $to = [];
    public array $cc = [];
    public array $bcc = [];
    public array $replyTo = [];
    public ?string $subject = null;
    public ?string $view = null;
    public ?string $textView = null;
    public array $viewData = [];
    public array $attachments = [];
    
    protected ?ViewFactoryInterface $viewFactory = null;

    public function send(MailerInterface $mailer): void
    {
        $this->build();
        
        $mailer->send($this->view, $this->buildViewData(), function (MailMessageInterface $message) {
            $this->buildMessage($message);
        });
    }

    abstract public function build(): void;

    public function setViewFactory(ViewFactoryInterface $viewFactory): self
    {
        $this->viewFactory = $viewFactory;
        return $this;
    }

    protected function buildViewData(): array
    {
        $data = $this->viewData;
        
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->getName() !== self::class) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }
        
        return $data;
    }

    protected function buildMessage(MailMessageInterface $message): void
    {
        if ($this->subject) {
            $message->subject($this->subject);
        }
        
        foreach ($this->to as $recipient) {
            $message->to($recipient['address'], $recipient['name']);
        }
        
        foreach ($this->cc as $recipient) {
            $message->cc($recipient['address'], $recipient['name']);
        }
        
        foreach ($this->bcc as $recipient) {
            $message->bcc($recipient['address'], $recipient['name']);
        }
        
        foreach ($this->attachments as $attachment) {
            $message->attach($attachment['file'], $attachment['options']);
        }

        // Generate text version if provided
        if ($this->textView && $this->viewFactory) {
            $message->text($this->viewFactory->make($this->textView, $this->buildViewData())->render());
        }
    }

    public function to(string $address, ?string $name = null): self
    {
        $this->to[] = compact('address', 'name');
        return $this;
    }

    public function cc(string $address, ?string $name = null): self
    {
        $this->cc[] = compact('address', 'name');
        return $this;
    }

    public function bcc(string $address, ?string $name = null): self
    {
        $this->bcc[] = compact('address', 'name');
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        $this->view = $view;
        $this->viewData = array_merge($this->viewData, $data);
        return $this;
    }

    public function text(string $textView, array $data = []): self
    {
        $this->textView = $textView;
        $this->viewData = array_merge($this->viewData, $data);
        return $this;
    }

    public function attach(string $file, array $options = []): self
    {
        $this->attachments[] = compact('file', 'options');
        return $this;
    }
}
