<?php

declare(strict_types=1);

namespace Palet\Framework\Organization;

use Palet\Framework\Contracts\Organization\OrganizationManagerInterface;
use Palet\Framework\Contracts\Organization\OrganizationInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Organization\Events\OrganizationCreated;
use Palet\Framework\Organization\Events\OrganizationArchived;

class OrganizationManager implements OrganizationManagerInterface
{
    public function __construct(protected ?EventDispatcherInterface $events = null) {}

    public function createOrganization(string $name, string|int $tenantId): OrganizationInterface
    {
        $org = new class($name, $tenantId) implements OrganizationInterface {
            public function __construct(private string $name, private string|int $tenantId) {}
            public function getId(): string|int { return uniqid('org_'); }
            public function getName(): string { return $this->name; }
            public function getTenantId(): string|int { return $this->tenantId; }
        };

        if ($this->events) {
            $this->events->dispatch(new OrganizationCreated($org));
        }

        return $org;
    }

    public function archiveOrganization(OrganizationInterface $organization): void
    {
        if ($this->events) {
            $this->events->dispatch(new OrganizationArchived($organization));
        }
    }
}
