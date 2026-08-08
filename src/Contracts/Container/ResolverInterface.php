<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Container;

/**
 * Amaç: IoC Container için bağımlılık çözümleme mantığını soyutlar.
 * Sorumluluk: Verilen bir sınıfın (veya Closure'ın) bağımlılıklarını Reflection veya diğer yöntemlerle analiz edip somutlaştırmak.
 * Kullanım Alanı: Container sınıfı içerisinde `make()` veya `build()` çağrıldığında çalışır.
 * Bağımlılıklar: Yok (Bağımsız çalışabilir)
 * Genişletilebilirlik: Gelecekte Attribute tabanlı (PHP 8) bağımlılık çözücüler eklenebilir.
 *
 * Örnek Kullanım:
 * $dependencies = $resolver->getDependencies(MyService::class);
 */
interface ResolverInterface
{
    /**
     * Sınıfın kurucu metodundaki (constructor) bağımlılıkları tespit eder.
     */
    public function getDependencies(string $concrete): array;
}
