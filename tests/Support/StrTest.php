<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Str;

class StrTest extends TestCase
{
    public function test_slug()
    {
        $this->assertEquals('hello-world', Str::slug('Hello World!'));
        $this->assertEquals('merhaba-dunya', Str::slug('Merhaba Dünya'));
    }

    public function test_camel_and_studly()
    {
        $this->assertEquals('helloWorld', Str::camel('hello_world'));
        $this->assertEquals('HelloWorld', Str::studly('hello_world'));
    }

    public function test_snake()
    {
        $this->assertEquals('hello_world', Str::snake('HelloWorld'));
    }

    public function test_uuid()
    {
        $uuid = Str::uuid();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }

    public function test_contains()
    {
        $this->assertTrue(Str::contains('hello world', 'world'));
        $this->assertTrue(Str::contains('hello world', ['foo', 'world']));
        $this->assertFalse(Str::contains('hello world', 'foo'));
    }
}
