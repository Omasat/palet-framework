<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Closure;

final readonly class ActionMetadata
{
    public bool $isClosure;
    public ?string $controllerClass;
    public ?string $method;
    public ?Closure $closure;

    public function __construct(
        bool $isClosure,
        ?string $controllerClass = null,
        ?string $method = null,
        ?Closure $closure = null
    ) {
        $this->isClosure = $isClosure;
        $this->controllerClass = $controllerClass;
        $this->method = $method;
        $this->closure = $closure;
    }
}
