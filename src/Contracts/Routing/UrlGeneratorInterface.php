<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing;

/**
 * Amaç: Uygulama içerisinde URL (link) üretilmesini sağlar.
 * Sorumluluk: Verilen bir isme (named route) veya path'e göre güvenli ve parametrik URL'ler oluşturmak.
 * Kullanım Alanı: Görünüm (View) dosyalarında, e-postalarda veya yönlendirme (Redirect) sınıflarında.
 * Bağımlılıklar: RouteCollectionInterface
 * Genişletilebilirlik: İmzalı URL (Signed URL) veya geçici (Temporary) URL üretimi gibi özellikler eklenebilir.
 *
 * Örnek Kullanım:
 * $url = $urlGenerator->route('user.profile', ['id' => 1]);
 */
interface UrlGeneratorInterface
{
    /**
     * Generate a absolute URL to the given path.
     */
    public function to(string $path, array $extra = []): string;

    /**
     * Get the URL to a named route.
     */
    public function route(string $name, array $parameters = []): string;
}
