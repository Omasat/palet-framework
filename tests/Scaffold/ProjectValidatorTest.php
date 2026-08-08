<?php

declare(strict_types=1);

namespace Tests\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scaffold\ProjectValidator;
use InvalidArgumentException;
use RuntimeException;

class ProjectValidatorTest extends TestCase
{
    public function test_throws_on_empty_path()
    {
        $validator = new ProjectValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate('   ');
    }

    public function test_throws_on_path_traversal()
    {
        $validator = new ProjectValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate('../some_dir');
    }

    public function test_throws_if_directory_not_empty()
    {
        $path = sys_get_temp_dir() . '/palet_test_dir_' . uniqid();
        mkdir($path);
        file_put_contents($path . '/test.txt', 'hello');

        $validator = new ProjectValidator();
        
        $this->expectException(RuntimeException::class);
        
        try {
            $validator->validate($path);
        } finally {
            unlink($path . '/test.txt');
            rmdir($path);
        }
    }
}
