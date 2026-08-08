<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Exceptions\FrameworkException;
use Palet\Framework\Exceptions\ConfigurationException;
use Palet\Framework\Exceptions\InvalidConfigurationException;
use Palet\Framework\Exceptions\ContainerException;
use Palet\Framework\Exceptions\BindingResolutionException;
use Palet\Framework\Exceptions\FileNotFoundException;
use Palet\Framework\Exceptions\ServiceProviderException;
use Palet\Framework\Exceptions\EnvironmentException;
use Palet\Framework\Exceptions\SecurityException;
use RuntimeException;

class CustomExceptionTest extends TestCase
{
    public function test_exceptions_inherit_from_runtime_exception()
    {
        $this->assertInstanceOf(RuntimeException::class, new FrameworkException());
        $this->assertInstanceOf(FrameworkException::class, new ConfigurationException());
        $this->assertInstanceOf(ConfigurationException::class, new InvalidConfigurationException());
        $this->assertInstanceOf(FrameworkException::class, new ContainerException());
        $this->assertInstanceOf(ContainerException::class, new BindingResolutionException());
        $this->assertInstanceOf(FrameworkException::class, new FileNotFoundException());
        $this->assertInstanceOf(FrameworkException::class, new ServiceProviderException());
        $this->assertInstanceOf(FrameworkException::class, new EnvironmentException());
        $this->assertInstanceOf(FrameworkException::class, new SecurityException());
    }
}
