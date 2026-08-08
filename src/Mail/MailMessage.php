<?php

declare(strict_types=1);

namespace Palet\Framework\Mail;

use Palet\Framework\Contracts\Mail\MailMessageInterface;

class MailMessage implements MailMessageInterface
{
    protected ?string $subject = null;
    protected array $from = [];
    protected array $to = [];
    protected array $cc = [];
    protected array $bcc = [];
    protected ?string $html = null;
    protected ?string $text = null;
    protected array $attachments = [];

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function from(string $address, ?string $name = null): self
    {
        $this->from = compact('address', 'name');
        return $this;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function to(string|array $address, ?string $name = null): self
    {
        if (is_array($address)) {
            $this->to = array_merge($this->to, $address);
            return $this;
        }

        $this->to[] = compact('address', 'name');
        return $this;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function cc(string|array $address, ?string $name = null): self
    {
        if (is_array($address)) {
            $this->cc = array_merge($this->cc, $address);
            return $this;
        }

        $this->cc[] = compact('address', 'name');
        return $this;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function bcc(string|array $address, ?string $name = null): self
    {
        if (is_array($address)) {
            $this->bcc = array_merge($this->bcc, $address);
            return $this;
        }

        $this->bcc[] = compact('address', 'name');
        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function html(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function text(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function attach(string $file, array $options = []): self
    {
        $this->attachments[] = compact('file', 'options');
        return $this;
    }
}
