<?php

declare(strict_types=1);

namespace Tests\Filesystem\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\Drivers\S3Driver;

class S3DriverTest extends TestCase
{
    public function test_instantiation_fails_without_aws_sdk()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Aws\S3\S3Client is required to use S3Driver.");
        
        $driver = new S3Driver(new \stdClass(), 'bucket-name');
    }
}
