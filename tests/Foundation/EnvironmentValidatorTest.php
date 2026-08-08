<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\EnvironmentValidator;
use RuntimeException;

class EnvironmentValidatorTest extends TestCase
{
    public function test_validate_passes_on_valid_environment()
    {
        // Because we lowered MINIMUM_PHP_VERSION to 8.2.0 and we have the extensions, this should just pass without exception.
        $this->expectNotToPerformAssertions();
        
        EnvironmentValidator::validate();
    }
}
