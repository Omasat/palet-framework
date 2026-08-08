<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

final readonly class CompiledRoute
{
    public string $regex;
    public array $variables;

    public function __construct(string $regex, array $variables)
    {
        $this->regex = $regex;
        $this->variables = $variables;
    }
}
