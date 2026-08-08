<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

class TemporaryDriver extends LocalDriver
{
    public function __construct(?string $root = null)
    {
        // Eğer özel bir root belirtilmemişse işletim sisteminin geçici dizinini kullanır.
        $root = $root ?? sys_get_temp_dir();
        parent::__construct($root);
    }
}
