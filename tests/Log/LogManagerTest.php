<?php

declare(strict_types=1);

namespace Tests\Log;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\LogManager;
use Palet\Framework\Log\LoggerFactory;
use Palet\Framework\Log\Drivers\NullDriver;

class LogManagerTest extends TestCase
{
    public function test_creates_and_caches_channels()
    {
        $factory = new LoggerFactory([
            'channels' => [
                'null' => ['driver' => 'null']
            ]
        ]);
        
        $manager = new LogManager('null', $factory);

        $channel1 = $manager->channel();
        $channel2 = $manager->channel('null');

        $this->assertInstanceOf(NullDriver::class, $channel1);
        $this->assertSame($channel1, $channel2);
    }

    public function test_implements_psr_3_logger()
    {
        $factory = new LoggerFactory([
            'channels' => [
                'null' => ['driver' => 'null']
            ]
        ]);
        
        $manager = new LogManager('null', $factory);
        
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $manager);

        // PSR-3 metotlarını test edelim (Hata vermemelidir)
        $manager->info('Test message', ['user' => 'admin']);
        $manager->error('Error message');
        $this->assertTrue(true);
    }
}
