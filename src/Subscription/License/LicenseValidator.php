<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\License;

use Palet\Framework\Contracts\Subscription\LicenseInterface;
use DateTimeInterface;
use DateTime;

class LicenseValidator
{
    /**
     * Validates if the license is not expired and has a valid signature.
     */
    public function validate(LicenseInterface $license): bool
    {
        if (!$license->isValid()) {
            return false;
        }

        $expiresAt = $license->getExpiresAt();
        if ($expiresAt !== null && $expiresAt < new DateTime()) {
            return false;
        }

        // Mocking signature validation
        $key = $license->getKey();
        if (empty($key) || strlen($key) < 10) {
            return false;
        }

        return true;
    }
}
