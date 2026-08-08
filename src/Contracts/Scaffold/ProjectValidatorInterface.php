<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scaffold;

interface ProjectValidatorInterface
{
    /**
     * Validate the target path to ensure it is safe and available.
     * Throws an exception if invalid.
     */
    public function validate(string $targetPath): void;
}
