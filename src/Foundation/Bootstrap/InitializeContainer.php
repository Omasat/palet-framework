<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class InitializeContainer implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        // Temel Container ayarlarını başlat. (EventDispatcher bağlamaları, vs.)
        // Application constructor'da zaten base bindingler yapıldığı için gerekirse ekstra çözümler burada yapılır.
    }
}
