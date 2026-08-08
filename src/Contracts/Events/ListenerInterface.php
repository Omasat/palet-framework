<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Events;

/**
 * Amaç: Belirli bir olayı dinleyen sınıfın sözleşmesidir.
 * Sorumluluk: Sadece kendisine gelen olayı işlemek.
 * Kullanım Alanı: EventDispatcher tarafından bir olay fırlatıldığında tetiklenir.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İsteğe bağlı olarak kuyruğa (Queue) atılabilir dinleyiciler için `ShouldQueueInterface` eklenebilir.
 *
 * Örnek Kullanım:
 * class SendWelcomeEmail implements ListenerInterface {
 *     public function handle(object $event): void { ... }
 * }
 */
interface ListenerInterface
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void;
}
