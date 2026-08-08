<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Mail;

interface MailMessageInterface
{
    /**
     * Get the subject of the message.
     */
    public function getSubject(): ?string;
    
    /**
     * Set the subject of the message.
     */
    public function subject(string $subject): self;

    /**
     * Get the sender of the message.
     */
    public function getFrom(): array;

    /**
     * Set the sender of the message.
     */
    public function from(string $address, ?string $name = null): self;

    /**
     * Get the recipients of the message.
     */
    public function getTo(): array;

    /**
     * Add a recipient to the message.
     */
    public function to(string $address, ?string $name = null): self;

    /**
     * Add a CC recipient to the message.
     */
    public function cc(string $address, ?string $name = null): self;
    
    /**
     * Get the CC recipients.
     */
    public function getCc(): array;

    /**
     * Add a BCC recipient to the message.
     */
    public function bcc(string $address, ?string $name = null): self;

    /**
     * Get the BCC recipients.
     */
    public function getBcc(): array;

    /**
     * Set the HTML body of the message.
     */
    public function html(string $html): self;

    /**
     * Get the HTML body of the message.
     */
    public function getHtml(): ?string;

    /**
     * Set the text body of the message.
     */
    public function text(string $text): self;
    
    /**
     * Get the text body of the message.
     */
    public function getText(): ?string;

    /**
     * Add an attachment to the message.
     */
    public function attach(string $file, array $options = []): self;

    /**
     * Get the attachments.
     */
    public function getAttachments(): array;
}
