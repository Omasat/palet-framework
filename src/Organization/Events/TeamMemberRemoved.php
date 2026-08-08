<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Events;

use Palet\Framework\Contracts\Organization\TeamInterface;

class TeamMemberRemoved
{
    public function __construct(public readonly TeamInterface $team, public readonly string|int $userId) {}
}
