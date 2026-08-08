<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Diagnostics\IDE\IDEHelperGenerator;

class IDEHelperGeneratorTest extends TestCase
{
    public function test_generates_helper_file()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'palet_ide_test';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        
        $generator = new IDEHelperGenerator();
        $generator->generate($dir);
        
        $expectedPath = $dir . DIRECTORY_SEPARATOR . '_ide_helper.php';
        $this->assertFileExists($expectedPath);
        
        $content = file_get_contents($expectedPath);
        $this->assertStringContainsString('namespace Palet\Framework\Support\Facades', $content);
        $this->assertStringContainsString('class Route', $content);
        
        unlink($expectedPath);
        rmdir($dir);
    }
}
