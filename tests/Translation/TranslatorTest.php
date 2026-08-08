<?php

declare(strict_types=1);

namespace Tests\Translation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Translation\Translator;
use Palet\Framework\Translation\FileLoader;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Translation\Events\TranslationMissing;
use Palet\Framework\Translation\Events\LocaleChanged;

class TranslatorTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/palet_lang_' . uniqid();
        mkdir($this->tempDir);
        mkdir($this->tempDir . '/en');
        mkdir($this->tempDir . '/tr');
        
        file_put_contents($this->tempDir . '/en/messages.php', "<?php return ['welcome' => 'Welcome, :name!', 'apples' => '{0} No apples|[1,19] Some apples|[20,*] Many apples'];");
        file_put_contents($this->tempDir . '/tr/messages.php', "<?php return ['welcome' => 'Hoşgeldin, :name!'];");
    }

    protected function tearDown(): void
    {
        @unlink($this->tempDir . '/en/messages.php');
        @unlink($this->tempDir . '/tr/messages.php');
        @rmdir($this->tempDir . '/en');
        @rmdir($this->tempDir . '/tr');
        @rmdir($this->tempDir);
    }

    public function test_get_translation()
    {
        $loader = new FileLoader($this->tempDir);
        $translator = new Translator($loader, 'en');
        
        $this->assertEquals('Welcome, John!', $translator->get('messages.welcome', ['name' => 'John']));
    }

    public function test_fallback_translation()
    {
        $loader = new FileLoader($this->tempDir);
        $translator = new Translator($loader, 'tr', 'en');
        
        $this->assertEquals('Hoşgeldin, Ali!', $translator->get('messages.welcome', ['name' => 'Ali']));
        // apples is missing in TR, should fallback to EN
        $this->assertEquals('{0} No apples|[1,19] Some apples|[20,*] Many apples', $translator->get('messages.apples'));
    }

    public function test_pluralization()
    {
        $loader = new FileLoader($this->tempDir);
        $translator = new Translator($loader, 'en');
        
        $this->assertEquals('No apples', $translator->choice('messages.apples', 0));
        $this->assertEquals('Some apples', $translator->choice('messages.apples', 5));
        $this->assertEquals('Many apples', $translator->choice('messages.apples', 50));
    }

    public function test_translation_missing_event()
    {
        $loader = new FileLoader($this->tempDir);
        $translator = new Translator($loader, 'en');
        
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with($this->isInstanceOf(TranslationMissing::class));
                   
        $translator->setEventDispatcher($dispatcher);
        
        $this->assertEquals('messages.missing', $translator->get('messages.missing'));
    }
}
