<?php

declare(strict_types=1);

namespace Tests\Environment;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Environment\EnvRepository;

class EnvRepositoryTest extends TestCase
{
    public function test_can_get_and_set_values()
    {
        $env = new EnvRepository(['APP_NAME' => 'Palet']);

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertEquals('Palet', $env->get('APP_NAME'));
        
        $env->set('APP_ENV', 'testing');
        $this->assertEquals('testing', $env->get('APP_ENV'));
        
        unset($_ENV['APP_ENV']);
        unset($_SERVER['APP_ENV']);
    }

    public function test_get_returns_default_if_not_found()
    {
        $env = new EnvRepository();

        $this->assertEquals('default_value', $env->get('NON_EXISTENT', 'default_value'));
    }

    public function test_falls_back_to_global_env()
    {
        $_ENV['GLOBAL_TEST_VAR'] = 'global_value';
        
        $env = new EnvRepository();
        
        $this->assertEquals('global_value', $env->get('GLOBAL_TEST_VAR'));
        
        unset($_ENV['GLOBAL_TEST_VAR']);
    }
}
