<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Mail;

interface MailerInterface
{
    /**
     * Send a new message using a view or a Mailable instance.
     */
    public function send(string|array|MailableInterface $view, array $data = [], \Closure|string|null $callback = null): void;

    /**
     * Set the global to address and name.
     */
    public function to(mixed $users, ?string $name = null): self;
    
    /**
     * Set the global from address and name.
     */
    public function from(string $address, ?string $name = null): self;
}
