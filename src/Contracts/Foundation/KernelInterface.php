<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation;

/**
 * Amaç: HTTP isteklerinin uygulamaya giriş noktası olan HTTP Kernel'i temsil eder.
 * Sorumluluk: Gelen isteği almak, middleware'lerden geçirmek, rotaya ulaştırmak ve yanıtı döndürmek.
 * Kullanım Alanı: index.php içerisinde uygulamanın boot edilip isteğin karşılanması aşamasında.
 * Bağımlılıklar: ApplicationInterface, RequestInterface (HTTP), ResponseInterface (HTTP)
 * Genişletilebilirlik: Console işlemleri için ayrı bir ConsoleKernelInterface oluşturulabilir.
 *
 * Örnek Kullanım:
 * $response = $kernel->handle($request);
 * $response->send();
 * $kernel->terminate($request, $response);
 */
interface KernelInterface
{
    /**
     * Bootstrap the application for HTTP requests.
     */
    public function bootstrap(): void;

    /**
     * Handle an incoming HTTP request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(mixed $request): mixed;

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function terminate(mixed $request, mixed $response): void;
}
