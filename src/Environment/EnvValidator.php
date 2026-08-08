<?php

declare(strict_types=1);

namespace Palet\Framework\Environment;

use RuntimeException;
use Palet\Framework\Contracts\Config\EnvironmentInterface;

class EnvValidator
{
    protected EnvironmentInterface $env;

    public function __construct(EnvironmentInterface $env)
    {
        $this->env = $env;
    }

    /**
     * @param string[] $variables
     * @throws RuntimeException
     */
    public function require(array $variables): void
    {
        $missing = [];

        foreach ($variables as $variable) {
            if (!$this->env->has($variable)) {
                $missing[] = $variable;
            }
        }

        if (!empty($missing)) {
            throw new RuntimeException(
                sprintf('The following environment variables are required but missing: %s', implode(', ', $missing))
            );
        }
    }
}
