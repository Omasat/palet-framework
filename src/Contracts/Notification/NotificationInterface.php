<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Notification;

interface NotificationInterface
{
    public function getId(): string|int;
    
    /**
     * @return array<string> Channels to send via, e.g. ['mail', 'sms']
     */
    public function getChannels(mixed $notifiable): array;
    
    public function getTemplateName(): string;
    
    public function getTemplateData(): array;
    
    public function isQueueable(): bool;
}
