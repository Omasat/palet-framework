<?php

declare(strict_types=1);

namespace Tests\Support\ValueObjects;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\ValueObjects\Email;
use Palet\Framework\Support\ValueObjects\IpAddress;
use Palet\Framework\Support\ValueObjects\Money;
use InvalidArgumentException;

class ValueObjectTest extends TestCase
{
    public function test_valid_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_invalid_email_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('invalid-email');
    }

    public function test_valid_ip()
    {
        $ip = new IpAddress('192.168.1.1');
        $this->assertEquals('192.168.1.1', (string) $ip);
    }

    public function test_invalid_ip_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new IpAddress('999.999.999.999');
    }

    public function test_money_addition()
    {
        $money = new Money(100);
        $added = $money->add(new Money(50));
        
        $this->assertEquals(150, $added->amount);
        $this->assertEquals('1,50 TRY', $added->format());
    }

    public function test_negative_money_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(-10);
    }

    public function test_money_addition_different_currencies_throws_exception()
    {
        $money = new Money(100, 'TRY');
        
        $this->expectException(InvalidArgumentException::class);
        $money->add(new Money(50, 'USD'));
    }
}
