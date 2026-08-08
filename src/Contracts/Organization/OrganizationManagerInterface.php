<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Organization;

interface OrganizationManagerInterface
{
    public function createOrganization(string $name, string|int $tenantId): OrganizationInterface;
    public function archiveOrganization(OrganizationInterface $organization): void;
}
