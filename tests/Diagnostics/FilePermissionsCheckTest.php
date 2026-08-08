<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Diagnostics\Checks\FilePermissionsCheck;

class FilePermissionsCheckTest extends TestCase
{
    public function test_passes_for_writable_directory()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'palet_test_writable';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        
        $check = new FilePermissionsCheck($dir);
        $this->assertTrue($check->check());
        
        rmdir($dir);
    }

    public function test_fails_for_missing_directory()
    {
        $check = new FilePermissionsCheck('/this/path/does/not/exist/12345');
        $this->assertFalse($check->check());
        $this->assertStringContainsString('Directory does not exist', $check->getErrorMessage());
    }
}
