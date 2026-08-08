<?php

declare(strict_types=1);

namespace Tests\Generator\Module;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Module\ModuleRegistrar;

class ModuleRegistrarTest extends TestCase
{
    protected string $statusPath;

    protected function setUp(): void
    {
        $this->statusPath = sys_get_temp_dir() . '/modules_statuses_' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->statusPath)) {
            unlink($this->statusPath);
        }
    }

    public function test_all_returns_empty_array_if_no_file()
    {
        $registrar = new ModuleRegistrar($this->statusPath);
        $this->assertEquals([], $registrar->all());
    }

    public function test_enables_and_disables_module()
    {
        $registrar = new ModuleRegistrar($this->statusPath);
        
        $registrar->enable('CRM');
        
        $modules = $registrar->all();
        $this->assertArrayHasKey('CRM', $modules);
        $this->assertTrue($modules['CRM']);
        
        $registrar->disable('CRM');
        
        $modules2 = $registrar->all();
        $this->assertArrayHasKey('CRM', $modules2);
        $this->assertFalse($modules2['CRM']);
    }
}
