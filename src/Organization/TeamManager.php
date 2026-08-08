<?php

declare(strict_types=1);

namespace Palet\Framework\Organization;

use Palet\Framework\Contracts\Organization\TeamInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Organization\Events\TeamCreated;
use Palet\Framework\Organization\Events\TeamMemberAssigned;
use Palet\Framework\Organization\Events\TeamMemberRemoved;

class TeamManager
{
    public function __construct(protected ?EventDispatcherInterface $events = null) {}

    public function createTeam(string $name, string|int $departmentId): TeamInterface
    {
        $team = new class($name, $departmentId) implements TeamInterface {
            public function __construct(private string $name, private string|int $departmentId) {}
            public function getId(): string|int { return uniqid('team_'); }
            public function getName(): string { return $this->name; }
            public function getDepartmentId(): string|int { return $this->departmentId; }
        };

        if ($this->events) {
            $this->events->dispatch(new TeamCreated($team));
        }

        return $team;
    }

    public function assignMember(TeamInterface $team, string|int $userId): void
    {
        if ($this->events) {
            $this->events->dispatch(new TeamMemberAssigned($team, $userId));
        }
    }

    public function removeMember(TeamInterface $team, string|int $userId): void
    {
        if ($this->events) {
            $this->events->dispatch(new TeamMemberRemoved($team, $userId));
        }
    }
}
