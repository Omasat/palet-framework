<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

/**
 * Amaç: Tek bir View (Görünüm) şablonunu temsil eder.
 * Sorumluluk: Şablona veri (data) göndermek, değişkenleri bağlamak ve nihai render edilmiş çıktıyı üretmek.
 * Kullanım Alanı: Controller metodlarında (return view('...')) kullanılarak istemciye gönderilecek HTML çıktısını oluşturur.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İhtiyaca göre özel View Composer'lar veya Creator'lar entegre edilebilir.
 *
 * Örnek Kullanım:
 * return $view->with('name', 'John')->render();
 */
interface ViewInterface
{
    /**
     * Get the string contents of the view.
     */
    public function render(): string;

    /**
     * Get the name of the view.
     */
    public function name(): string;

    /**
     * Add a piece of data to the view.
     */
    public function with(string|array $key, mixed $value = null): self;
}
