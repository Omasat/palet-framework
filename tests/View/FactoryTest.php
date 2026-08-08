<?php

declare(strict_types=1);

namespace Tests\View;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Factory;
use Palet\Framework\View\FileViewFinder;
use Palet\Framework\View\Engines\PhpEngine;
use Palet\Framework\View\Engines\CompilerEngine;
use Palet\Framework\View\Compiler\TemplateCompiler;

class FactoryTest extends TestCase
{
    protected string $fixturesPath;
    protected Factory $factory;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures';
        $finder = new FileViewFinder([$this->fixturesPath]);
        $this->factory = new Factory($finder);
        
        $this->factory->addEngine('php', new PhpEngine());
        $this->factory->addEngine('palet.php', new CompilerEngine(new TemplateCompiler($this->fixturesPath . '/cache')));
        
        file_put_contents($this->fixturesPath . '/test.php', 'Hello <?php echo $name; ?>');
    }

    protected function tearDown(): void
    {
        unlink($this->fixturesPath . '/test.php');
    }

    public function test_exists()
    {
        $this->assertTrue($this->factory->exists('test'));
        $this->assertFalse($this->factory->exists('missing'));
    }

    public function test_make_and_render()
    {
        $view = $this->factory->make('test', ['name' => 'World']);
        $this->assertEquals('Hello World', $view->render());
    }

    public function test_shared_data()
    {
        $this->factory->share('global', 'SharedValue');
        $view = $this->factory->make('test', ['name' => 'Local']);
        
        $this->assertEquals('SharedValue', $this->factory->getShared()['global']);
        $this->assertEquals('Local', $view->getData()['name']);
    }

    public function test_section_management()
    {
        $this->factory->startSection('content');
        echo "Section Content";
        $this->factory->stopSection();
        
        $this->assertEquals('Section Content', $this->factory->yieldContent('content'));
        $this->assertEquals('Default', $this->factory->yieldContent('missing', 'Default'));
    }
}
