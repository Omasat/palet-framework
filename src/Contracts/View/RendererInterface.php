<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

/**
 * Amaç: Farklı şablon motorlarının (Template Engine) ortak sözleşmesidir.
 * Sorumluluk: Verilen bir şablon dosyasını alıp, verilerle derleyerek HTML/String formatına dönüştürmek.
 * Kullanım Alanı: View Factory içerisinde Blade, Twig veya düz PHP dosyalarını render etmek için kullanılır.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İhtiyaca göre yeni bir şablon motoru ekleneceğinde (Örn: Twig) bu arayüzü uygulaması yeterlidir.
 *
 * Örnek Kullanım:
 * $html = $renderer->render('home', ['name' => 'John']);
 */
interface RendererInterface
{
    /**
     * Get the evaluated contents of the view.
     */
    public function render(string $view, array $data = []): string;
}
