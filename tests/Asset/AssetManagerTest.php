<?php

declare(strict_types=1);

namespace Tests\Asset;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Asset\AssetManager;
use Palet\Framework\Contracts\Asset\AssetAdapterInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;
use Palet\Framework\View\Html\HtmlString;

class AssetManagerTest extends TestCase
{
    public function test_resolves_assets_through_adapter()
    {
        $manager = new AssetManager();
        
        $adapter = new class implements AssetAdapterInterface {
            public function __invoke(string|array $assets): HtmlStringInterface
            {
                return new HtmlString('<script src="test.js"></script>');
            }
        };
        
        $manager->setAdapter($adapter);
        
        $html = $manager->resolve('test.js');
        
        $this->assertEquals('<script src="test.js"></script>', $html->toHtml());
        $this->assertSame($adapter, $manager->getAdapter());
    }

    public function test_throws_exception_if_no_adapter_configured()
    {
        $this->expectException(\RuntimeException::class);
        
        $manager = new AssetManager();
        $manager->resolve('test.js');
    }
}
