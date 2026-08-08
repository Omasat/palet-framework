<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Exceptions\SecurityMasker;

class SecurityMaskerTest extends TestCase
{
    public function test_masks_sensitive_data()
    {
        $masker = new SecurityMasker();

        $data = [
            'username' => 'admin',
            'password' => 'secret123',
            'APP_KEY' => 'base64:something',
            'db_PASSWORD' => 'root',
            'safe_token' => 'abc', // Key has token so it gets masked
            'nested' => [
                'api_key' => '123456',
                'public_data' => 'hello'
            ]
        ];

        $masked = $masker->mask($data);

        $this->assertEquals('admin', $masked['username']);
        $this->assertEquals('[MASKED]', $masked['password']);
        $this->assertEquals('[MASKED]', $masked['APP_KEY']);
        $this->assertEquals('[MASKED]', $masked['db_PASSWORD']);
        $this->assertEquals('[MASKED]', $masked['safe_token']);
        $this->assertEquals('[MASKED]', $masked['nested']['api_key']);
        $this->assertEquals('hello', $masked['nested']['public_data']);
    }
}
