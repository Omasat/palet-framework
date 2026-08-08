<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Amaç: Gelen HTTP isteğini (Request) temsil eder ve çerçeveye özgü yardımcı metotlar sunar.
 * Sorumluluk: PSR-7 ServerRequestInterface sözleşmesini genişleterek, input alma, cookie okuma ve session'a erişim gibi ek framework kolaylıkları (DX) sağlamak.
 * Kullanım Alanı: Controller metodlarında, Middleware'lerde ve FormRequest doğrulama sınıflarında.
 * Bağımlılıklar: Psr\Http\Message\ServerRequestInterface
 * Genişletilebilirlik: İhtiyaca göre API, Web, GraphQL istekleri için ayrı arayüzler türetilebilir.
 *
 * Örnek Kullanım:
 * $email = $request->input('email');
 */
interface RequestInterface extends ServerRequestInterface
{
    /**
     * Retrieve an input item from the request.
     */
    public function input(string $key, mixed $default = null): mixed;

    /**
     * Retrieve a file from the request.
     */
    public function file(string $key): mixed;

    /**
     * Determine if the request is an AJAX request.
     */
    public function isXmlHttpRequest(): bool;

    /**
     * Get the bearer token from the request headers.
     */
    public function bearerToken(): ?string;
}
