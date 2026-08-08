<?php

declare(strict_types=1);

namespace Tests\Notification;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Notification\NotificationDispatcher;
use Palet\Framework\Notification\NotificationManager;
use Palet\Framework\Notification\NotificationChannelManager;
use Palet\Framework\Notification\NotificationPreferences;
use Palet\Framework\Notification\Templates\TemplateEngine;
use Palet\Framework\Contracts\Notification\NotificationInterface;
use Palet\Framework\Contracts\Notification\ChannelInterface;

class ChannelDispatchTest extends TestCase
{
    public function test_notification_dispatches_to_multiple_channels()
    {
        $channelManager = new NotificationChannelManager();
        
        $mailSent = false;
        $dbSent = false;
        
        $mailChannel = new class($mailSent) implements ChannelInterface {
            public function __construct(public bool &$sent) {}
            public function send(mixed $notifiable, NotificationInterface $notification, string $content): void { $this->sent = true; }
            public function getName(): string { return 'mail'; }
        };
        
        $dbChannel = new class($dbSent) implements ChannelInterface {
            public function __construct(public bool &$sent) {}
            public function send(mixed $notifiable, NotificationInterface $notification, string $content): void { $this->sent = true; }
            public function getName(): string { return 'database'; }
        };

        $channelManager->registerChannel($mailChannel);
        $channelManager->registerChannel($dbChannel);
        
        $preferences = new NotificationPreferences();
        $templateEngine = new TemplateEngine();
        $templateEngine->registerTemplate('welcome', 'Hello');
        
        $dispatcher = new NotificationDispatcher($channelManager, $templateEngine, $preferences);
        $manager = new NotificationManager($dispatcher);
        
        $notification = new class implements NotificationInterface {
            public function getId(): string|int { return 1; }
            public function getChannels(mixed $notifiable): array { return ['mail', 'database']; }
            public function getTemplateName(): string { return 'welcome'; }
            public function getTemplateData(): array { return []; }
            public function isQueueable(): bool { return false; }
        };
        
        $notifiable = (object)['id' => 1];
        
        $manager->send($notifiable, $notification);
        
        $this->assertTrue($mailSent);
        $this->assertTrue($dbSent);
    }
}
