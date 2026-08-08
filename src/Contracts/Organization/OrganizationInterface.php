<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Organization;

interface OrganizationInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getTenantId(): string|int;
}
