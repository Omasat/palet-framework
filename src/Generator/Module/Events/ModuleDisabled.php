<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Module\Events;

class ModuleDisabled
{
    public function __construct(public readonly string $moduleName) {}
}
