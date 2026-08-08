<?php

declare(strict_types=1);

namespace Tests\Environment;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Environment\EnvParser;

class EnvParserTest extends TestCase
{
    public function test_parses_basic_variables()
    {
        $parser = new EnvParser();
        $content = "APP_NAME=Palet\nAPP_DEBUG=true\nAPP_ENV=local\n";

        $parsed = $parser->parse($content);

        $this->assertEquals('Palet', $parsed['APP_NAME']);
        $this->assertTrue($parsed['APP_DEBUG']);
        $this->assertEquals('local', $parsed['APP_ENV']);
    }

    public function test_parses_boolean_and_null_values()
    {
        $parser = new EnvParser();
        $content = "IS_TRUE=true\nIS_FALSE=false\nIS_NULL=null\nIS_EMPTY=empty\n";

        $parsed = $parser->parse($content);

        $this->assertTrue($parsed['IS_TRUE']);
        $this->assertFalse($parsed['IS_FALSE']);
        $this->assertNull($parsed['IS_NULL']);
        $this->assertEquals('', $parsed['IS_EMPTY']);
    }

    public function test_parses_numeric_values()
    {
        $parser = new EnvParser();
        $content = "INT_VAL=123\nFLOAT_VAL=123.45\n";

        $parsed = $parser->parse($content);

        $this->assertSame(123, $parsed['INT_VAL']);
        $this->assertSame(123.45, $parsed['FLOAT_VAL']);
    }

    public function test_parses_quoted_values()
    {
        $parser = new EnvParser();
        $content = "APP_NAME=\"Palet Framework\"\nSECRET='super_secret_key'\nIS_TRUE_STRING=\"true\"\n";

        $parsed = $parser->parse($content);

        $this->assertEquals('Palet Framework', $parsed['APP_NAME']);
        $this->assertEquals('super_secret_key', $parsed['SECRET']);
        $this->assertEquals('true', $parsed['IS_TRUE_STRING']); // Quoted values should be returned as string, not boolean
    }

    public function test_ignores_comments_and_empty_lines()
    {
        $parser = new EnvParser();
        $content = "\n# This is a comment\nAPP_NAME=Palet\n\n\n# Another comment\n";

        $parsed = $parser->parse($content);

        $this->assertCount(1, $parsed);
        $this->assertEquals('Palet', $parsed['APP_NAME']);
    }
}
