<?php

declare(strict_types=1);

namespace Tests\Environment;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Environment\EnvValidator;
use Palet\Framework\Environment\EnvRepository;
use RuntimeException;

class EnvValidatorTest extends TestCase
{
    public function test_passes_if_required_variables_exist()
    {
        $env = new EnvRepository(['APP_KEY' => 'secret']);
        $validator = new EnvValidator($env);

        $this->expectNotToPerformAssertions();
        $validator->require(['APP_KEY']);
    }

    public function test_throws_exception_if_required_variables_missing()
    {
        $env = new EnvRepository(['APP_KEY' => 'secret']);
        $validator = new EnvValidator($env);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The following environment variables are required but missing: DB_HOST, DB_USER');

        $validator->require(['APP_KEY', 'DB_HOST', 'DB_USER']);
    }
}
