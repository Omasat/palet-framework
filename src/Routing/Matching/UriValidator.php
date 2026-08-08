<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Routing\Matching\ValidatorInterface;
use Palet\Framework\Contracts\Routing\RouteInterface;

class UriValidator implements ValidatorInterface
{
    protected RouteCompiler $compiler;

    public function __construct(?RouteCompiler $compiler = null)
    {
        $this->compiler = $compiler ?? new RouteCompiler();
    }

    public function matches(RouteInterface $route, RequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();
        $path = $path === '' ? '/' : $path;

        $compiled = $this->compiler->compile($route);

        return preg_match($compiled->regex, $path) === 1;
    }

    /**
     * Extract parameters from the request path.
     */
    public function extractParameters(RouteInterface $route, RequestInterface $request): array
    {
        $path = $request->getUri()->getPath();
        $path = $path === '' ? '/' : $path;

        $compiled = $this->compiler->compile($route);
        $parameters = [];

        if (preg_match($compiled->regex, $path, $matches)) {
            foreach ($compiled->variables as $variable) {
                if (isset($matches[$variable]) && $matches[$variable] !== '') {
                    $parameters[$variable] = $matches[$variable];
                }
            }
        }

        return $parameters;
    }
}
