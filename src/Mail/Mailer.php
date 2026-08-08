<?php

declare(strict_types=1);

namespace Palet\Framework\Mail;

use Palet\Framework\Contracts\Mail\MailerInterface;
use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailableInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;
use Palet\Framework\Contracts\View\ViewFactoryInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Mail\Events\MailSending;
use Palet\Framework\Mail\Events\MailSent;

class Mailer implements MailerInterface
{
    protected MailTransportInterface $transport;
    protected ?ViewFactoryInterface $views = null;
    protected ?EventDispatcherInterface $events = null;
    protected array $globalTo = [];
    protected array $globalFrom = [];

    public function __construct(MailTransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function setViewFactory(ViewFactoryInterface $views): void
    {
        $this->views = $views;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function to(mixed $users, ?string $name = null): self
    {
        if (!is_array($users)) {
            $users = [['address' => $users, 'name' => $name]];
        }
        
        $this->globalTo = $users;
        return $this;
    }

    public function from(string $address, ?string $name = null): self
    {
        $this->globalFrom = compact('address', 'name');
        return $this;
    }

    public function send(string|array|MailableInterface $view, array $data = [], \Closure|string|null $callback = null): void
    {
        if ($view instanceof MailableInterface) {
            $this->sendMailable($view);
            return;
        }

        $message = new MailMessage();
        
        if (!empty($this->globalFrom)) {
            $message->from($this->globalFrom['address'], $this->globalFrom['name']);
        }
        
        if (!empty($this->globalTo)) {
            foreach ($this->globalTo as $recipient) {
                $message->to($recipient['address'], $recipient['name'] ?? null);
            }
        }

        if ($callback instanceof \Closure) {
            $callback($message);
        }

        if (is_string($view) && $this->views !== null) {
            $message->html($this->views->make($view, $data)->render());
        } elseif (is_array($view) && $this->views !== null) {
            if (isset($view['html'])) {
                $message->html($this->views->make($view['html'], $data)->render());
            }
            if (isset($view['text'])) {
                $message->text($this->views->make($view['text'], $data)->render());
            }
        }

        if ($this->events) {
            $this->events->dispatch(new MailSending($message));
        }

        $this->transport->send($message);

        if ($this->events) {
            $this->events->dispatch(new MailSent($message));
        }
    }

    protected function sendMailable(MailableInterface $mailable): void
    {
        if (method_exists($mailable, 'setViewFactory') && $this->views) {
            $mailable->setViewFactory($this->views);
        }
        
        $mailable->send($this);
    }
}
