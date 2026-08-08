<?php

declare(strict_types=1);

namespace Tests\Organization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Organization\TeamManager;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Organization\Events\TeamCreated;
use Palet\Framework\Organization\Events\TeamMemberAssigned;
use Palet\Framework\Organization\Events\TeamMemberRemoved;

class TeamManagementTest extends TestCase
{
    public function test_create_team_dispatches_event()
    {
        $eventDispatched = false;
        $dispatcher = new class($eventDispatched) implements EventDispatcherInterface {
            public function __construct(public bool &$eventDispatched) {}
            public function dispatch(object $event): object {
                if ($event instanceof TeamCreated) {
                    $this->eventDispatched = true;
                }
                return $event;
            }
            public function listen(string $event, callable|string $listener, int $priority = 0): void {}
            public function hasListeners(string $event): bool { return false; }
            public function forget(string $event): void {}
            public function subscribe(object|string $subscriber): void {}
            public function dispatchUntil(object|string $event, mixed $payload = []): mixed { return null; }
        };

        $manager = new TeamManager($dispatcher);
        $team = $manager->createTeam('Backend Devs', 10);
        
        $this->assertEquals('Backend Devs', $team->getName());
        $this->assertEquals(10, $team->getDepartmentId());
        $this->assertTrue($eventDispatched);
    }

    public function test_assign_member_dispatches_event()
    {
        $eventDispatched = false;
        $dispatcher = new class($eventDispatched) implements EventDispatcherInterface {
            public function __construct(public bool &$eventDispatched) {}
            public function dispatch(object $event): object {
                if ($event instanceof TeamMemberAssigned) {
                    $this->eventDispatched = true;
                }
                return $event;
            }
            public function listen(string $event, callable|string $listener, int $priority = 0): void {}
            public function hasListeners(string $event): bool { return false; }
            public function forget(string $event): void {}
            public function subscribe(object|string $subscriber): void {}
            public function dispatchUntil(object|string $event, mixed $payload = []): mixed { return null; }
        };

        $manager = new TeamManager($dispatcher);
        $team = $manager->createTeam('Frontend', 20);
        
        $manager->assignMember($team, 99);
        
        $this->assertTrue($eventDispatched);
    }
}
