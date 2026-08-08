<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization\Traits;

use Palet\Framework\Contracts\Authorization\GateInterface;

trait Authorizable
{
    /** @var GateInterface */
    protected static GateInterface $gate;

    public static function setGate(GateInterface $gate): void
    {
        static::$gate = $gate;
    }

    public function can(string $ability, mixed $arguments = []): bool
    {
        if (isset(static::$gate)) {
            // Need to pass the user instance somehow, standard Gate inspects the logged in user.
            // If the gate is properly wired, it knows about the user.
            return static::$gate->allows($ability, $arguments);
        }
        return false;
    }

    public function cannot(string $ability, mixed $arguments = []): bool
    {
        return !$this->can($ability, $arguments);
    }
}
