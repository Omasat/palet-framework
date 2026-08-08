<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Organization;

interface BranchInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getOrganizationId(): string|int;
}
