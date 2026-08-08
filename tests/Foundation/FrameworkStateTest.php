<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\FrameworkState;

class FrameworkStateTest extends TestCase
{
    public function test_enum_has_all_expected_states()
    {
        $expected = [
            'Booting',
            'Bootstrapping',
            'RegisteringProviders',
            'BootingProviders',
            'Ready',
            'Maintenance',
            'Error',
            'Terminated'
        ];

        $actual = array_map(fn($case) => $case->name, FrameworkState::cases());

        $this->assertEquals($expected, $actual);
    }
}
