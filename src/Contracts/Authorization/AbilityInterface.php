<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface AbilityInterface
{
    public function getName(): string;
    public function getResource(): mixed;
}
