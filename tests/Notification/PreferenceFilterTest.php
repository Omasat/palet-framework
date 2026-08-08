<?php

declare(strict_types=1);

namespace Tests\Notification;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Notification\NotificationDispatcher;
use Palet\Framework\Notification\NotificationChannelManager;
use Palet\Framework\Notification\NotificationPreferences;
use Palet\Framework\Notification\Templates\TemplateEngine;
use Palet\Framework\Contracts\Notification\NotificationInterface;
use Palet\Framework\Contracts\Notification\ChannelInterface;

class PreferenceFilterTest extends TestCase
{
    public function test_user_preference_blocks_channel()
    {
        $channelManager = new NotificationChannelManager();
        
        $smsSent = false;
        
        $smsChannel = new class($smsSent) implements ChannelInterface {
            public function __construct(public bool &$sent) {}
            public function send(mixed $notifiable, NotificationInterface $notification, string $content): void { $this->sent = true; }
            public function getName(): string { return 'sms'; }
        };

        $channelManager->registerChannel($smsChannel);
        
        $preferences = new NotificationPreferences();
        // User 1 disabled SMS
        $preferences->setPreferences(1, ['sms' => false]);
        
        $templateEngine = new TemplateEngine();
        $templateEngine->registerTemplate('alert', 'Alert!');
        
        $dispatcher = new NotificationDispatcher($channelManager, $templateEngine, $preferences);
        
        $notification = new class implements NotificationInterface {
            public function getId(): string|int { return 1; }
            public function getChannels(mixed $notifiable): array { return ['sms']; }
            public function getTemplateName(): string { return 'alert'; }
            public function getTemplateData(): array { return []; }
            public function isQueueable(): bool { return false; }
        };
        
        $notifiable = (object)['id' => 1];
        
        $dispatcher->dispatch($notifiable, $notification);
        
        $this->assertFalse($smsSent); // Blocked by preference
    }
}
