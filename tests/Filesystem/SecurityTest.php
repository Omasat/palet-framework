<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\PathNormalizer;
use InvalidArgumentException;

class SecurityTest extends TestCase
{
    public function test_path_normalizer_removes_relative_segments()
    {
        $path = 'a/b/../c/./d.txt';
        $normalized = PathNormalizer::normalize($path);
        
        $this->assertEquals('a/c/d.txt', $normalized);
    }

    public function test_path_normalizer_prevents_directory_escape()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Path traversal detected');
        
        PathNormalizer::normalize('../../etc/passwd');
    }

    public function test_path_normalizer_prevents_sneaky_escape()
    {
        $this->expectException(InvalidArgumentException::class);
        
        PathNormalizer::normalize('folder/../../etc/passwd');
    }
}
