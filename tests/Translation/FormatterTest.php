<?php

declare(strict_types=1);

namespace Tests\Translation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Translation\Formatters\NumberFormatter;
use Palet\Framework\Translation\Formatters\DateTimeFormatter;
use Palet\Framework\Translation\Formatters\CurrencyFormatter;

class FormatterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('IntlDateFormatter')) {
            $this->markTestSkipped('The intl extension is not available.');
        }
    }

    public function test_number_formatter()
    {
        $formatter = new NumberFormatter();
        
        $en = $formatter->format(1234.56, 'en_US');
        $tr = $formatter->format(1234.56, 'tr_TR');
        
        // en_US: 1,234.56 or 1,234.561 depending on decimals
        $this->assertStringContainsString('1,234.56', $en);
        // tr_TR: 1.234,56
        $this->assertStringContainsString('1.234,56', $tr);
    }

    public function test_currency_formatter()
    {
        $formatter = new CurrencyFormatter();
        
        $en = $formatter->format(1234.56, 'en_US', ['currency' => 'USD']);
        
        $this->assertStringContainsString('1,234.56', $en);
        $this->assertStringContainsString('$', $en);
    }

    public function test_date_formatter()
    {
        $formatter = new DateTimeFormatter();
        
        $date = new \DateTime('2026-08-05 15:00:00');
        
        $en = $formatter->format($date, 'en_US', ['pattern' => 'yyyy-MM-dd']);
        $this->assertEquals('2026-08-05', $en);
    }
}
