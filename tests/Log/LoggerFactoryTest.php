<?php

declare(strict_types=1);

namespace Tests\Log;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\LoggerFactory;
use Palet\Framework\Log\Drivers\FileDriver;
use Palet\Framework\Log\Drivers\DailyFileDriver;
use Palet\Framework\Log\Drivers\StackDriver;
use Palet\Framework\Log\Drivers\NullDriver;
use Palet\Framework\Log\Drivers\EmergencyDriver;
use InvalidArgumentException;

class LoggerFactoryTest extends TestCase
{
    public function test_creates_drivers_based_on_config()
    {
        $config = [
            'channels' => [
                'single' => ['driver' => 'single', 'path' => 'single.log'],
                'daily' => ['driver' => 'daily', 'path' => 'daily.log'],
                'null' => ['driver' => 'null'],
                'emergency' => ['driver' => 'emergency'],
                'stack' => ['driver' => 'stack', 'channels' => ['single', 'null']],
            ]
        ];

        $factory = new LoggerFactory($config);

        $this->assertInstanceOf(FileDriver::class, $factory->createDriver('single'));
        $this->assertInstanceOf(DailyFileDriver::class, $factory->createDriver('daily'));
        $this->assertInstanceOf(NullDriver::class, $factory->createDriver('null'));
        $this->assertInstanceOf(EmergencyDriver::class, $factory->createDriver('emergency'));
        $this->assertInstanceOf(StackDriver::class, $factory->createDriver('stack'));
    }

    public function test_throws_exception_if_channel_not_defined()
    {
        $factory = new LoggerFactory(['channels' => []]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Log channel [not_exists] is not defined.");

        $factory->createDriver('not_exists');
    }

    public function test_throws_exception_if_driver_not_supported()
    {
        $factory = new LoggerFactory([
            'channels' => [
                'custom' => ['driver' => 'unsupported']
            ]
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Log driver [unsupported] is not supported.");

        $factory->createDriver('custom');
    }
}
