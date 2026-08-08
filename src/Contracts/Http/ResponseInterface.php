<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Amaç: HTTP yanıtını (Response) temsil eder.
 * Sorumluluk: PSR-7 ResponseInterface sözleşmesini koruyarak JSON, View veya Redirect gibi kolaylık (DX) sağlayıcı metotları tanımlamak.
 * Kullanım Alanı: Controller dönüşlerinde (return) ve Middleware zincirinden dışarıya çıkan son çıktıda.
 * Bağımlılıklar: Psr\Http\Message\ResponseInterface
 * Genişletilebilirlik: PDF veya Stream (Video) yanıtları için özel alt arayüzler eklenebilir.
 *
 * Örnek Kullanım:
 * return $response->json(['status' => 'success']);
 */
interface ResponseInterface extends PsrResponseInterface
{
    /**
     * Send the HTTP response to the client.
     */
    public function send(): void;
    
    /**
     * Set a cookie on the response.
     */
    public function cookie(string $name, string $value, int $minutes = 0): self;
}
