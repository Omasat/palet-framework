<?php

declare(strict_types=1);

namespace Tests\Database\Seeders;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Seeders\Seeder;
use Palet\Framework\Database\Seeders\SeederRunner;

class RoleSeeder extends Seeder
{
    public static bool $ran = false;
    
    public function run(): void
    {
        self::$ran = true;
    }
}

class UserSeeder extends Seeder
{
    public static bool $ran = false;
    
    public function run(): void
    {
        self::$ran = true;
        $this->call([RoleSeeder::class]);
    }
}

class SeederRunnerTest extends TestCase
{
    protected function setUp(): void
    {
        RoleSeeder::$ran = false;
        UserSeeder::$ran = false;
    }

    public function test_seeder_runner_executes_seeders_and_handles_nested_calls()
    {
        $runner = new SeederRunner();
        
        $runner->run(UserSeeder::class);
        
        $this->assertTrue(UserSeeder::$ran);
        $this->assertTrue(RoleSeeder::$ran);
    }
}
