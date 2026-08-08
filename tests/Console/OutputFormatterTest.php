<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Formatter\OutputFormatter;

class OutputFormatterTest extends TestCase
{
    public function test_formats_tags_with_ansi_codes()
    {
        $formatter = new OutputFormatter();
        
        $text = "<info>Success!</info>";
        $formatted = $formatter->format($text);
        
        // Info tag maps to 32 (green) and unset to 39
        $expected = "\033[32mSuccess!\033[39m";
        
        $this->assertEquals($expected, $formatted);
    }
}
