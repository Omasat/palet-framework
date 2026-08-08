<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\PathResolver;
use InvalidArgumentException;

class PathResolverSecurityTest extends TestCase
{
    public function test_resolves_valid_path()
    {
        $resolver = new PathResolver();
        $path = $resolver->resolve('/var/www/storage', 'app/file.txt');
        
        $this->assertEquals('/var/www/storage' . DIRECTORY_SEPARATOR . 'app/file.txt', $path);
    }

    public function test_blocks_directory_traversal()
    {
        $resolver = new PathResolver();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Directory traversal detected in path: ../../etc/passwd");
        
        $resolver->resolve('/var/www/storage', '../../etc/passwd');
    }
}
