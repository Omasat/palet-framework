<?php

declare(strict_types=1);

namespace Tests\Subscription;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Subscription\License\LicenseValidator;
use Palet\Framework\Contracts\Subscription\LicenseInterface;
use DateTime;

class LicenseValidatorTest extends TestCase
{
    protected function createLicense(bool $isValid, string $key, ?DateTime $expiresAt = null)
    {
        return new class($isValid, $key, $expiresAt) implements LicenseInterface {
            public function __construct(private bool $valid, private string $key, private ?DateTime $expiresAt) {}
            public function getKey(): string { return $this->key; }
            public function isValid(): bool { return $this->valid; }
            public function getExpiresAt(): ?\DateTimeInterface { return $this->expiresAt; }
            public function getMetadata(): array { return []; }
        };
    }

    public function test_validates_good_license()
    {
        $validator = new LicenseValidator();
        $license = $this->createLicense(true, 'VALID-KEY-12345', new DateTime('+1 year'));
        
        $this->assertTrue($validator->validate($license));
    }

    public function test_rejects_expired_license()
    {
        $validator = new LicenseValidator();
        $license = $this->createLicense(true, 'VALID-KEY-12345', new DateTime('-1 day'));
        
        $this->assertFalse($validator->validate($license));
    }

    public function test_rejects_invalid_key()
    {
        $validator = new LicenseValidator();
        $license = $this->createLicense(true, 'SHORT', new DateTime('+1 year'));
        
        $this->assertFalse($validator->validate($license));
    }
}
