<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Version;

class VersionTest extends TestCase
{
    public function test_version_constants()
    {
        $this->assertEquals('0.1.0', Version::getVersion());
        $this->assertEquals('1000', Version::getBuild());
        $this->assertEquals('alpha', Version::getReleaseChannel());
        $this->assertEquals('8.2.0', Version::getMinimumPhpVersion());
    }
}
