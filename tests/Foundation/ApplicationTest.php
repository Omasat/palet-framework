<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\FrameworkState;

class ApplicationTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = __DIR__;
    }

    public function test_application_initializes_paths_correctly()
    {
        $app = new Application($this->basePath);

        $this->assertEquals($this->basePath, $app->basePath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'app', $app->appPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'config', $app->configPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'storage', $app->storagePath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'public', $app->publicPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'resources', $app->resourcesPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'routes', $app->routesPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'bootstrap', $app->bootstrapPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'lang', $app->langPath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'vendor', $app->vendorPath());
    }

    public function test_application_can_add_custom_path()
    {
        $app = new Application($this->basePath);
        
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models', $app->appPath('Models'));
    }

    public function test_application_is_bound_in_container()
    {
        $app = new Application($this->basePath);

        $this->assertSame($app, $app->make('app'));
        $this->assertSame($app, $app->make(\Palet\Framework\Contracts\Foundation\ApplicationInterface::class));
    }

    public function test_application_state_transitions()
    {
        $app = new Application($this->basePath);
        
        // Başlangıç durumu Booting'dir
        $this->assertEquals(FrameworkState::Booting, $app->getState());
        
        // Boot sonrası Ready olur
        $app->boot();
        $this->assertEquals(FrameworkState::Ready, $app->getState());
        
        // İkinci boot çağrısı durumu değiştirmez
        $app->setState(FrameworkState::Terminated);
        $app->boot();
        $this->assertEquals(FrameworkState::Terminated, $app->getState());
    }
}
