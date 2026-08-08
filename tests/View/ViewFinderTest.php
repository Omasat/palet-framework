<?php

declare(strict_types=1);

namespace Tests\View;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\FileViewFinder;
use InvalidArgumentException;

class ViewFinderTest extends TestCase
{
    protected string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures';
        file_put_contents($this->fixturesPath . '/home.palet.php', 'home template');
        file_put_contents($this->fixturesPath . '/about.php', 'about template');
        if (!is_dir($this->fixturesPath . '/admin')) {
            mkdir($this->fixturesPath . '/admin');
        }
        file_put_contents($this->fixturesPath . '/admin/dashboard.palet.php', 'dashboard');
    }

    protected function tearDown(): void
    {
        unlink($this->fixturesPath . '/home.palet.php');
        unlink($this->fixturesPath . '/about.php');
        unlink($this->fixturesPath . '/admin/dashboard.palet.php');
        rmdir($this->fixturesPath . '/admin');
    }

    public function test_finds_palet_views()
    {
        $finder = new FileViewFinder([$this->fixturesPath]);
        $path = $finder->find('home');
        
        $this->assertEquals(str_replace('\\', '/', $this->fixturesPath) . '/home.palet.php', str_replace('\\', '/', $path));
    }

    public function test_finds_php_views()
    {
        $finder = new FileViewFinder([$this->fixturesPath]);
        $path = $finder->find('about');
        
        $this->assertEquals(str_replace('\\', '/', $this->fixturesPath) . '/about.php', str_replace('\\', '/', $path));
    }

    public function test_finds_nested_views()
    {
        $finder = new FileViewFinder([$this->fixturesPath]);
        $path = $finder->find('admin.dashboard');
        
        $this->assertEquals(str_replace('\\', '/', $this->fixturesPath) . '/admin/dashboard.palet.php', str_replace('\\', '/', $path));
    }

    public function test_throws_exception_if_not_found()
    {
        $this->expectException(InvalidArgumentException::class);
        $finder = new FileViewFinder([$this->fixturesPath]);
        $finder->find('nonexistent');
    }
}
