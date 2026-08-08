<?php

declare(strict_types=1);

namespace Tests\View\Components;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Factory;
use Palet\Framework\View\FileViewFinder;
use Palet\Framework\View\Engines\CompilerEngine;
use Palet\Framework\View\Compiler\TemplateCompiler;
use Palet\Framework\View\Components\Slot;
use Palet\Framework\View\Components\AttributeBag;

class FactoryComponentTest extends TestCase
{
    protected string $fixturesPath;
    protected Factory $factory;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/../fixtures';
        $finder = new FileViewFinder([$this->fixturesPath]);
        $this->factory = new Factory($finder);
        
        $this->factory->addEngine('palet.php', new CompilerEngine(new TemplateCompiler($this->fixturesPath . '/cache')));
        
        // component template
        file_put_contents($this->fixturesPath . '/alert.palet.php', '<div {!! $attributes->merge(["class" => "alert"]) !!}>{!! $title ?? "" !!}{{ $slot }}</div>');
    }

    protected function tearDown(): void
    {
        unlink($this->fixturesPath . '/alert.palet.php');
        
        $files = glob($this->fixturesPath . '/cache/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function test_renders_component_with_slots_and_attributes()
    {
        $this->factory->startComponent('alert', ['class' => 'bg-red']);
        
        $this->factory->slot('title');
        echo "<strong>Error!</strong>";
        $this->factory->endSlot();
        
        echo " Something went wrong.";
        
        $html = $this->factory->renderComponent();
        
        $this->assertEquals('<div class="bg-red alert"><strong>Error!</strong> Something went wrong.</div>', $html);
    }
}
